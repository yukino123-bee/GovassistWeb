<?php

use App\Services\DocumentOcrVerifier;
use Illuminate\Support\Facades\Storage;

function renderTextImage(string $text): string
{
    $small = imagecreatetruecolor(420, 40);
    imagefill($small, 0, 0, imagecolorallocate($small, 255, 255, 255));
    imagestring($small, 5, 8, 12, $text, imagecolorallocate($small, 0, 0, 0));

    $large = imagecreatetruecolor(1680, 160);
    imagecopyresized($large, $small, 0, 0, 0, 0, 1680, 160, 420, 40);

    ob_start();
    imagepng($large);

    return (string) ob_get_clean();
}

beforeEach(function () {
    Storage::fake('public');
    Storage::disk('public')->put('templates/t.png', renderTextImage('BARANGAY CERTIFICATE OF INDIGENCY'));
});

test('the real review script never fails hard when it cannot read a document', function () {
    Storage::disk('public')->put('documents/d.png', renderTextImage('BARANGAY CERTIFICATE OF INDIGENCY').'x');

    $result = app(DocumentOcrVerifier::class)->verify('public', 'documents/d.png', 'templates/t.png', 'Barangay, Indigency');

    expect($result['status'])->toBeIn(['verified', 'review', 'mismatch'])
        ->and($result)->toHaveKeys(['message', 'found', 'missing', 'issues']);
});

test('an identical upload is verified without OCR', function () {
    Storage::disk('public')->put('documents/d.png', Storage::disk('public')->get('templates/t.png'));

    $result = app(DocumentOcrVerifier::class)->verify('public', 'documents/d.png', 'templates/t.png', 'Barangay, Indigency');

    expect($result['status'])->toBe('verified')->and($result['method'])->toBe('exact_file_match');
});

test('tesseract reads a different photo of the right document as verified and the wrong one as mismatch', function () {
    $pypdf = shell_exec('python3 -c "import PIL" 2>&1');

    if (! trim((string) shell_exec('command -v tesseract')) || $pypdf) {
        $this->markTestSkipped('tesseract and Pillow are required for the end-to-end OCR check (installed in CI).');
    }

    Storage::disk('public')->put('documents/right.png', renderTextImage('BARANGAY CERTIFICATE OF INDIGENCY - JUAN'));
    Storage::disk('public')->put('documents/wrong.png', renderTextImage('PHILHEALTH MEMBER DATA RECORD'));

    $verifier = app(DocumentOcrVerifier::class);

    expect($verifier->verify('public', 'documents/right.png', 'templates/t.png', 'Barangay, Indigency')['status'])->toBe('verified')
        ->and($verifier->verify('public', 'documents/wrong.png', 'templates/t.png', 'Barangay, Indigency')['status'])->toBe('mismatch');
});
