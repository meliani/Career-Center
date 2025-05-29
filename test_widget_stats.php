<?php

use App\Services\ProjectStatisticsService;
use App\Models\Year;

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$year = Year::current();
$service = new ProjectStatisticsService($year);

echo "=== Testing Widget Statistics ===\n";
echo "Alternative calculation = FALSE (assigned department):\n";
$stats1 = $service->getWidgetStatistics(false);
foreach ($stats1 as $stat) {
    echo "  {$stat['name']}: {$stat['count']} projects, {$stat['professors_count']} professors, Ratio: " . round($stat['ratio'], 2) . "\n";
}

echo "\nAlternative calculation = TRUE (professor department):\n";
$stats2 = $service->getWidgetStatistics(true);
foreach ($stats2 as $stat) {
    echo "  {$stat['name']}: {$stat['count']} projects, {$stat['professors_count']} professors, Ratio: " . round($stat['ratio'], 2) . "\n";
}

echo "\n=== Expected from Integrity Command ===\n";
echo "EMO: 67 projects (by professor dept)\n";
echo "MIR: 149 projects (by professor dept)\n";
echo "GLC: 82 projects (by professor dept)\n";
echo "SC: 110 projects (by professor dept)\n";
