<?php declare(strict_types=1);

/*
 * Checks a PHPUnit Clover report against coverage-threshold.json. Fails when line or method coverage is under its
 * minimum, or when a source file with executable lines has none covered.
 *
 * Usage: php ci/check-coverage.php <clover.xml>
 * Exit codes: 0 when coverage passes, 1 when it does not, 2 when the report or thresholds cannot be read.
 */

$report = isset($argv[1]) ? simplexml_load_file($argv[1]) : false;
$threshold = json_decode((string) file_get_contents(__DIR__ . '/../coverage-threshold.json'), true);

// Clover metric pairs (total, covered) for each threshold in coverage-threshold.json.
$checks = [
    'line' => ['statements', 'coveredstatements', 'minimum_line_coverage'],
    'method' => ['methods', 'coveredmethods', 'minimum_method_coverage'],
];

if (false === $report || !is_array($threshold) || !isset($report->project->metrics)) {
    fwrite(STDERR, "Could not judge: needs a readable Clover report argument and a readable coverage-threshold.json.\n");
    exit(2);
}

$minimums = [];

foreach ($checks as $name => [, , $key]) {
    $value = $threshold[$key] ?? null;

    if (!is_numeric($value)) {
        fwrite(STDERR, sprintf("Could not judge: coverage-threshold.json has no numeric %s.\n", $key));
        exit(2);
    }

    $minimums[$name] = (float) $value;
}

$failed = false;

foreach ($report->xpath('//file') ?: [] as $file) {
    if ((int) $file->metrics['statements'] > 0 && 0 === (int) $file->metrics['coveredstatements']) {
        fwrite(STDERR, sprintf("Uncovered file: %s\n", $file['name']));
        $failed = true;
    }
}

foreach ($checks as $name => [$totalMetric, $coveredMetric]) {
    $total = (int) $report->project->metrics[$totalMetric];
    $covered = (int) $report->project->metrics[$coveredMetric];
    $coverage = 0 === $total ? 100.0 : 100 * $covered / $total;
    $minimum = $minimums[$name];

    printf("%s coverage: %.2f%% (%d/%d), minimum %s%%.\n", ucfirst($name), $coverage, $covered, $total, $minimum);

    if ($coverage < $minimum) {
        fwrite(STDERR, sprintf("%s coverage is under the minimum.\n", ucfirst($name)));
        $failed = true;
    }
}

exit($failed ? 1 : 0);
