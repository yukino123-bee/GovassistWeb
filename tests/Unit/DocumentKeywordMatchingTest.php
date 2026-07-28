<?php

use Symfony\Component\Process\Process;

test('document keyword matching accepts all configured keywords across OCR variations', function () {
    $scriptPath = dirname(__DIR__, 2).'/scripts/compare_images.py';
    $python = <<<'PYTHON'
import importlib.util
import json
import sys

spec = importlib.util.spec_from_file_location("compare_images", sys.argv[1])
module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(module)

print(json.dumps({
    "exact": module.search_keywords(
        "Republic of the Philippines MEDICAL-CERTIFICATE Patient Name Juan Dela Cruz Attending Physician",
        "Medical Certificate, Patient Name, Physician",
    ),
    "duplicate": module.search_keywords(
        "Barangay Certificate of Indigency",
        "Indigency,Indigency",
    ),
    "ocr_variation": module.search_keywords(
        "Medica1 Certificate Patient Narne Juan Dela Cruz PhysIcian",
        "Medical Certificate,Patient Name,Physician",
    ),
    "missing": module.search_keywords(
        "Medical Certificate Patient Name Juan Dela Cruz",
        "Medical Certificate,Patient Name,Physician",
    ),
    "short_keyword_boundary": module.search_keywords(
        "This is a valid certificate",
        "ID",
    ),
}))
PYTHON;

    $process = new Process(['python3', '-c', $python, $scriptPath]);
    $process->mustRun();

    expect(json_decode($process->getOutput(), true))
        ->toBe([
            'exact' => true,
            'duplicate' => true,
            'ocr_variation' => true,
            'missing' => false,
            'short_keyword_boundary' => false,
        ]);
});
