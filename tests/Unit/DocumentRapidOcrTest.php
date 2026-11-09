<?php

use Symfony\Component\Process\Process;

function rapidOcrInstalled(): bool
{
    $process = new Process(['python3', '-c', 'import rapidocr_onnxruntime']);
    $process->run();

    return $process->isSuccessful();
}

test('rapidocr reads printed text from an image', function () {
    $scriptPath = dirname(__DIR__, 2).'/scripts/document_review.py';
    $python = <<<'PYTHON'
import importlib.util, json, sys, tempfile
from PIL import Image, ImageDraw, ImageFont

spec = importlib.util.spec_from_file_location("document_review", sys.argv[1])
module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(module)

image = Image.new("RGB", (1200, 400), "white")
draw = ImageDraw.Draw(image)
font = ImageFont.load_default(size=44)
draw.text((40, 60), "Barangay Certificate of Indigency", fill="black", font=font)
draw.text((40, 140), "This is to certify that Juan Dela Cruz", fill="black", font=font)
path = tempfile.NamedTemporaryFile(suffix=".png", delete=False).name
image.save(path)

result = module.extract_image(path)
print(json.dumps({"engine": result["engine"], "text": result["text"]}))
PYTHON;

    $process = new Process(['python3', '-c', $python, $scriptPath]);
    $process->mustRun();
    $result = json_decode($process->getOutput(), true);

    expect($result['engine'])->toBe('rapidocr')
        ->and($result['text'])->toContain('Indigency');
})->skip(fn () => ! rapidOcrInstalled(), 'rapidocr is not installed (see scripts/requirements-ocr.txt)');
