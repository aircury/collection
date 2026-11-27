<?php declare(strict_types=1);

/*
 * Fails when a change loosens a coverage minimum: every minimum_* figure in coverage-threshold.json must be at least
 * what the target branch records, compared one by one, and none may be removed.
 *
 * Usage: php ci/check-thresholds.php <coverage-threshold.json content on the target branch>
 *        (an empty argument means the target branch has no such file, so there is nothing to compare)
 * Exit codes: 0 when no minimum was loosened, 1 when one was, 2 when the figures cannot be read.
 */

$current = json_decode((string) file_get_contents(__DIR__ . '/../coverage-threshold.json'), true);
$baseJson = $argv[1] ?? null;

if (!is_array($current) || !is_string($baseJson)) {
    fwrite(STDERR, "Could not judge: needs the target branch's coverage-threshold.json as an argument and a readable local copy.\n");
    exit(2);
}

if ('' === trim($baseJson)) {
    echo "The target branch has no coverage-threshold.json: nothing to compare.\n";
    exit(0);
}

$base = json_decode($baseJson, true);

if (!is_array($base)) {
    fwrite(STDERR, "Could not judge: the target branch's coverage-threshold.json is not valid JSON.\n");
    exit(2);
}

$failed = false;

foreach ($base as $key => $baseValue) {
    if (!str_starts_with((string) $key, 'minimum_') || !is_numeric($baseValue)) {
        continue;
    }

    $value = $current[$key] ?? null;

    if (!is_numeric($value)) {
        fwrite(STDERR, sprintf("%s was removed; the target branch requires %s.\n", $key, $baseValue));
        $failed = true;
    } elseif ((float) $value < (float) $baseValue) {
        fwrite(STDERR, sprintf("%s was lowered from %s to %s.\n", $key, $baseValue, $value));
        $failed = true;
    } else {
        printf("%s: %s (target branch: %s).\n", $key, $value, $baseValue);
    }
}

exit($failed ? 1 : 0);
