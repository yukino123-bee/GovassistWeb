<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DocumentOcrVerifier
{
    /**
     * @return array{matched: bool, message: string, method: string, score: float}
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
                return [
                    'matched' => true,
                    'message' => 'Document verified successfully.',
                    'method' => 'exact_file_match',
                    'score' => 1,
                ];
            }

            $result = Process::timeout(120)->run([
                'python3',
                base_path('scripts/compare_images.py'),
                $localDocumentPath,
                $localTemplatePath,
                $keywords,
            ]);

            if ($result->failed()) {
                return $this->failureResult('The document could not be verified. Please upload a clearer copy or contact the facilitator.');
            }

            $verification = json_decode($result->output(), true);

            if (! is_array($verification) || ! array_key_exists('match', $verification)) {
                return $this->failureResult('The OCR verifier returned an invalid result. Please try again.');
            }

            return [
                'matched' => $verification['match'] === true,
                'message' => (string) ($verification['note'] ?? 'The document does not match the configured template or required keywords.'),
                'method' => (string) ($verification['method'] ?? 'unknown'),
                'score' => (float) ($verification['score'] ?? 0),
            ];
        } catch (Throwable) {
            return $this->failureResult('The document could not be verified. Please upload a clearer copy or contact the facilitator.');
        } finally {
            foreach ($temporaryPaths as $temporaryPath) {
                if (is_file($temporaryPath)) {
                    unlink($temporaryPath);
                }
            }
        }
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

    /**
     * @return array{matched: false, message: string, method: string, score: float}
     */
    private function failureResult(string $message): array
    {
        return [
            'matched' => false,
            'message' => $message,
            'method' => 'verification_error',
            'score' => 0,
        ];
    }
}
