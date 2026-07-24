<?php

use App\Models\GovernmentService;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$services = GovernmentService::with(['requirements.template'])->get();
foreach ($services as $s) {
    echo $s->service_name.': ';
    foreach ($s->requirements as $r) {
        echo $r->name_en.', ';
    }
    echo "\n";
}
