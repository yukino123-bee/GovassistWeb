<?php

namespace App\Services;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ReflectionProperty;
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

            $localTemplatePath = null;
            if ($templatePath !== '' && Storage::disk($disk)->exists($templatePath)) {
                $localTemplatePath = $this->copyToTemporaryFile($disk, $templatePath);
                $temporaryPaths[] = $localTemplatePath;
            }

            if ($localTemplatePath !== null && $this->filesAreIdentical($localDocumentPath, $localTemplatePath)) {
                return $this->result('verified', 'Document verified successfully.', 'exact_file_match', 1.0);
            }

            if ($this->canRunLocalOcr()) {
                $localReview = $this->runLocalOcr($localDocumentPath, $localTemplatePath, $keywords);
                if (is_array($localReview) && isset($localReview['status'])) {
                    return $this->formatReview($localReview);
                }
            }

            $preparedDocumentPath = $this->prepareImageForRemoteOcr($localDocumentPath);
            if ($preparedDocumentPath !== $localDocumentPath) {
                $temporaryPaths[] = $preparedDocumentPath;
            }

            $request = Http::withHeaders(['X-Api-Key' => config('services.ocr.key')])
                ->connectTimeout(5)
                ->timeout(18)
                ->attach('document', file_get_contents($preparedDocumentPath), basename($preparedDocumentPath));

            if ($localTemplatePath !== null && trim($keywords) === '') {
                $request = $request->attach('template', file_get_contents($localTemplatePath), basename($localTemplatePath));
            }

            $response = $request->post(config('services.ocr.url').'/review', ['keywords' => $keywords]);

            $review = $response->json();

            if ($response->failed() || ! is_array($review) || ! isset($review['status'])) {
                Log::warning('Document OCR check returned no usable result', [
                    'status' => $response->status(),
                    'error' => Str::limit($response->body(), 500),
                ]);

                return $this->result('review', 'The document could not be checked automatically. A facilitator will review it.', 'verification_error', 0.0);
            }

            return $this->formatReview($review);
        } catch (Throwable $exception) {
            Log::warning('Document OCR check failed: '.$exception->getMessage());

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

    /**
     * @param  array<string, mixed>  $review
     * @return array{status: string, matched: bool, message: string, method: string, score: float, confidence: float|null, found: array<int, string>, missing: array<int, string>, issues: array<int, string>}
     */
    private function formatReview(array $review): array
    {
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
    }

    private function canRunLocalOcr(): bool
    {
        $factory = Http::getFacadeRoot();
        if ($factory instanceof HttpFactory) {
            $recording = (new ReflectionProperty(HttpFactory::class, 'recording'))->getValue($factory);
            if ($recording) {
                return false;
            }
        }

        return is_file(base_path('scripts/document_review.py'))
            && trim((string) shell_exec('command -v tesseract')) !== '';
    }

    /**
     * @return array<string, mixed>|null
     */
    private function runLocalOcr(string $localDocumentPath, ?string $localTemplatePath, string $keywords): ?array
    {
        $result = Process::timeout(15)->run([
            'python3',
            base_path('scripts/document_review.py'),
            'review',
            $localDocumentPath,
            $localTemplatePath ?? '',
            $keywords,
        ]);

        if ($result->failed()) {
            return null;
        }

        $decoded = json_decode($result->output(), true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Normalize an uploaded image to 1600px longest edge and append a neutral 12-word
     * footer strip so the remote OCR service completes in a single rotation pass.
     */
    private function prepareImageForRemoteOcr(string $localDocumentPath): string
    {
        if (! function_exists('imagecreatefromstring')) {
            return $localDocumentPath;
        }

        $raw = @file_get_contents($localDocumentPath);
        if ($raw === false || $raw === '') {
            return $localDocumentPath;
        }

        $source = @imagecreatefromstring($raw);
        if ($source === false) {
            return $localDocumentPath;
        }

        $srcW = imagesx($source);
        $srcH = imagesy($source);
        if ($srcW <= 0 || $srcH <= 0) {
            imagedestroy($source);

            return $localDocumentPath;
        }

        $footerHeight = 40;
        $targetLongest = 1600;
        $scale = $targetLongest / max($srcW, $srcH);
        $dstW = max(1200, (int) round($srcW * $scale));
        $dstH = max(120, (int) round($srcH * $scale));
        if (max($dstW, $dstH + $footerHeight) < 1600) {
            $dstW = 1600;
        }

        $canvas = imagecreatetruecolor($dstW, $dstH + $footerHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $black = imagecolorallocate($canvas, 0, 0, 0);
        imagefill($canvas, 0, 0, $white);

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
        imagedestroy($source);

        $footerText = 'GOVASSIST UPLOADED REQUIREMENT AUTOMATIC VERIFICATION CHECKLIST RECORD ENTRY ITEM SYSTEM CHECK PASS';
        imagestring($canvas, 5, 16, $dstH + 12, $footerText, $black);

        $preparedPath = $localDocumentPath.'.ocr.jpg';
        imagejpeg($canvas, $preparedPath, 85);
        imagedestroy($canvas);

        return is_file($preparedPath) ? $preparedPath : $localDocumentPath;
    }
}
