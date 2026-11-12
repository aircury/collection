<?php declare(strict_types=1);

/*
 * Checks a PHPUnit Clover report against coverage-threshold.json. Fails when line coverage is under the minimum, or
 * when a source file with executable lines has none covered.
 *
 * Usage: php ci/check-coverage.php <clover.xml>
 */

$report = isset($argv[1]) ? simplexml_load_file($argv[1]) : false;
$threshold = json_decode((string) file_get_contents(__DIR__ . '/../coverage-threshold.json'), true);
$minimum = is_array($threshold) ? $threshold['minimum_line_coverage'] ?? null : null;

if (false === $report || !is_numeric($minimum)) {
    fwrite(STDERR, "Needs a Clover report argument and a numeric minimum_line_coverage in coverage-threshold.json.\n");
    exit(1);
}

$failed = false;

foreach ($report->xpath('//file') ?: [] as $file) {
    if ((int) $file->metrics['statements'] > 0 && 0 === (int) $file->metrics['coveredstatements']) {
        fwrite(STDERR, sprintf("Uncovered file: %s\n", $file['name']));
        $failed = true;
    }
}

$lines = (int) $report->project->metrics['statements'];
$covered = (int) $report->project->metrics['coveredstatements'];
$coverage = 0 === $lines ? 100.0 : 100 * $covered / $lines;

printf("Line coverage: %.2f%% (%d/%d lines), minimum %s%%.\n", $coverage, $covered, $lines, $minimum);

if ($coverage < (float) $minimum) {
    fwrite(STDERR, "Line coverage is under the minimum.\n");
    $failed = true;
}

exit($failed ? 1 : 0);
