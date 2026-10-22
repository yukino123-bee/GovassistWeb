<?php

use Symfony\Component\Process\Process;

function runDocumentReviewScenarios(): array
{
    $scriptPath = dirname(__DIR__, 2).'/scripts/document_review.py';
    $python = <<<'PYTHON'
import importlib.util
import json
import os
import sys
import tempfile

spec = importlib.util.spec_from_file_location("document_review", sys.argv[1])
module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(module)

document = tempfile.NamedTemporaryFile(suffix=".png", delete=False).name
template = tempfile.NamedTemporaryFile(suffix=".png", delete=False).name

def fake(text, confidence=90.0):
    return {"text": text, "confidence": confidence, "words": len(text.split()), "engine": "fake", "issues": []}

def scenario(doc_text, keywords, template_text=None, confidence=90.0):
    calls = {document: fake(doc_text, confidence), template: fake(template_text or "")}
    module.extract_any = lambda path: calls[path]
    return module.review(document, template if template_text is not None else None, keywords)["status"]

long_text = "Republic of the Philippines Barangay Certificate of Indigency this is to certify that Juan Dela Cruz is a resident of this barangay and belongs to an indigent family"
unrelated = "Monthly electricity bill account number statement period total amount due payable before the due date to the nearest collection center thank you for your payment"

out = {
    "all_found": scenario(long_text, "Barangay, Indigency, Certificate"),
    "ocr_noise": scenario(long_text.replace("Indigency", "lndigency"), "Indigency"),
    "partial": scenario(long_text, "Barangay, Indigency, Physician, Patient"),
    "wrong_document": scenario(unrelated, "Barangay, Indigency, Certificate"),
    "wrong_but_blurry": scenario(unrelated, "Barangay, Indigency, Certificate", confidence=30.0),
    "newline_keywords": scenario(long_text, "Barangay\nIndigency"),
    "template_only_match": scenario(long_text, "", template_text=long_text),
    "template_only_wrong": scenario(unrelated, "", template_text=long_text),
    "no_rules": scenario(long_text, ""),
}

module.extract_any = lambda path: fake("hi")
out["unreadable"] = module.review(document, None, "Barangay")["status"]
print(json.dumps(out))
PYTHON;

    $process = new Process(['python3', '-c', $python, $scriptPath]);
    $process->mustRun();

    return json_decode($process->getOutput(), true);
}

test('document review decides verified, review or mismatch', function () {
    expect(runDocumentReviewScenarios())->toBe([
        'all_found' => 'verified',
        'ocr_noise' => 'verified',
        'partial' => 'review',
        'wrong_document' => 'mismatch',
        'wrong_but_blurry' => 'review',
        'newline_keywords' => 'verified',
        'template_only_match' => 'verified',
        'template_only_wrong' => 'mismatch',
        'no_rules' => 'review',
        'unreadable' => 'review',
    ]);
});

test('document review flags blurry or tiny images before reading them', function () {
    $scriptPath = dirname(__DIR__, 2).'/scripts/document_review.py';
    $python = <<<'PYTHON'
import importlib.util, json, sys, tempfile
import numpy as np, cv2
spec = importlib.util.spec_from_file_location("document_review", sys.argv[1])
module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(module)

sharp = np.full((1400, 1000), 255, dtype=np.uint8)
for row in range(100, 1300, 40):
    cv2.putText(sharp, "Barangay Certificate", (80, row), cv2.FONT_HERSHEY_SIMPLEX, 1.0, 0, 2)
blurry = cv2.GaussianBlur(sharp, (51, 51), 0)
tiny = cv2.resize(sharp, (300, 420))

print(json.dumps({
    "sharp": module.assess_quality(sharp, cv2, np),
    "blurry": len(module.assess_quality(blurry, cv2, np)),
    "tiny": len(module.assess_quality(tiny, cv2, np)),
}))
PYTHON;

    $process = new Process(['python3', '-c', $python, $scriptPath]);
    $process->mustRun();
    $result = json_decode($process->getOutput(), true);

    expect($result['sharp'])->toBe([])
        ->and($result['blurry'])->toBeGreaterThan(0)
        ->and($result['tiny'])->toBeGreaterThan(0);
});
