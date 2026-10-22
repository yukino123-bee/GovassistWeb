<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DocumentOcrVerifier
{
    /**
     * Review an uploaded document against an administrator template and its required words.
     *
     * Status is one of: verified (safe to auto-approve), review (a facilitator must check),
     * mismatch (readable and clearly the wrong document).
     *
     * @return array{status: string, matched: bool, message: string, method: string, score: float, confidence: float|null, found: array<int, string>, missing: array<int, string>, issues: array<int, string>}
     */
    public function verify(string $disk, string $documentPath, string $templatePath, string $keywords): array
    {
        $temporaryPaths = [];

        try {
            $localDocumentPath = $this->copyToTemporaryFile($disk, $documentPath);
            $temporaryPaths[] = $localDocumentPath;

            $localTemplatePath = $this->copyToTemporaryFile($disk, $templatePath);
            $temporaryPaths[] = $localTemplatePath;

            if ($this->filesAreIdentical($localDocumentPath, $localTemplatePath)) {
                return $this->result('verified', 'Document verified successfully.', 'exact_file_match', 1.0);
            }

            $result = Process::timeout(180)->run([
                'python3',
                base_path('scripts/document_review.py'),
                'review',
                $localDocumentPath,
                $localTemplatePath,
                $keywords,
            ]);

            $review = json_decode($result->output(), true);

            if ($result->failed() || ! is_array($review) || ! isset($review['status'])) {
                return $this->result('review', 'The document could not be checked automatically. A facilitator will review it.', 'verification_error', 0.0);
            }

            return [
                'status' => in_array($review['status'], ['verified', 'review', 'mismatch'], true) ? $review['status'] : 'review',
                'matched' => $review['status'] === 'verified',
                'message' => (string) ($review['message'] ?? ''),
                'method' => (string) ($review['method'] ?? 'unknown'),
                'score' => (float) ($review['score'] ?? 0),
                'confidence' => isset($review['confidence']) ? (float) $review['confidence'] : null,
                'found' => array_values($review['found'] ?? []),
                'missing' => array_values($review['missing'] ?? []),
                'issues' => array_values($review['issues'] ?? []),
            ];
        } catch (Throwable) {
            return $this->result('review', 'The document could not be checked automatically. A facilitator will review it.', 'verification_error', 0.0);
        } finally {
            foreach ($temporaryPaths as $temporaryPath) {
                if (is_file($temporaryPath)) {
                    unlink($temporaryPath);
                }
            }
        }
    }

    /**
     * Plain-language summary stored on the checklist item for the facilitator.
     *
     * @param  array{status: string, message: string, found: array<int, string>, missing: array<int, string>, issues: array<int, string>}  $verification
     */
    public function describe(array $verification): string
    {
        $lines = [$verification['message']];

        if ($verification['found']) {
            $lines[] = 'Found: '.implode(', ', $verification['found']).'.';
        }

        if ($verification['missing'] && $verification['status'] !== 'mismatch') {
            $lines[] = 'Not found: '.implode(', ', $verification['missing']).'.';
        }

        foreach ($verification['issues'] as $issue) {
            $lines[] = $issue;
        }

        return implode(' ', array_filter($lines));
    }

    /**
     * @return array{status: string, matched: bool, message: string, method: string, score: float, confidence: float|null, found: array<int, string>, missing: array<int, string>, issues: array<int, string>}
     */
    private function result(string $status, string $message, string $method, float $score): array
    {
        return [
            'status' => $status,
            'matched' => $status === 'verified',
            'message' => $message,
            'method' => $method,
            'score' => $score,
            'confidence' => null,
            'found' => [],
            'missing' => [],
            'issues' => [],
        ];
    }

    private function copyToTemporaryFile(string $disk, string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $temporaryPath = tempnam(sys_get_temp_dir(), 'govassist-ocr-');

        if ($temporaryPath === false) {
            throw new \RuntimeException('Unable to create a temporary OCR file.');
        }

        $pathWithExtension = $temporaryPath.($extension !== '' ? '.'.$extension : '');
        rename($temporaryPath, $pathWithExtension);

        $source = Storage::disk($disk)->readStream($path);
        $destination = fopen($pathWithExtension, 'wb');

        if ($source === null || $destination === false) {
            if (is_resource($source)) {
                fclose($source);
            }

            if (is_resource($destination)) {
                fclose($destination);
            }

            unlink($pathWithExtension);

            throw new \RuntimeException('Unable to read a document for OCR.');
        }

        try {
            stream_copy_to_stream($source, $destination);
        } catch (Throwable $exception) {
            unlink($pathWithExtension);

            throw $exception;
        } finally {
            fclose($source);
            fclose($destination);
        }

        return $pathWithExtension;
    }

    private function filesAreIdentical(string $documentPath, string $templatePath): bool
    {
        $documentSize = filesize($documentPath);
        $templateSize = filesize($templatePath);

        if ($documentSize === false || $documentSize === 0 || $documentSize !== $templateSize) {
            return false;
        }

        $documentHash = hash_file('sha256', $documentPath);
        $templateHash = hash_file('sha256', $templatePath);

        return is_string($documentHash)
            && is_string($templateHash)
            && hash_equals($documentHash, $templateHash);
    }
}
