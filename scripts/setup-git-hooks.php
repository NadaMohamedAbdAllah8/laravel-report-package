<?php

$root = dirname(__DIR__);
$gitDir = $root . DIRECTORY_SEPARATOR . '.git';
$versionedHooksDir = $root . DIRECTORY_SEPARATOR . '.githooks';

if (! is_dir($gitDir)) {
    // Not a git checkout; nothing to do.
    return;
}

if (! is_dir($versionedHooksDir)) {
    @mkdir($versionedHooksDir, 0777, true);
}

// Point this repository's hooks path to the versioned directory
// Best-effort; ignore failures (e.g., git not on PATH)
try {
    // Use proc_open to avoid shell specifics
    $cmd = ['git', 'config', 'core.hooksPath', '.githooks'];
    // On Windows, proc_open is preferable; but exec is fine for simple cases
    @exec(implode(' ', array_map('escapeshellarg', $cmd)));
} catch (Throwable $e) {
    // noop
}

// Ensure pre-commit is executable on POSIX systems
$preCommit = $versionedHooksDir . DIRECTORY_SEPARATOR . 'pre-commit';
if (is_file($preCommit)) {
    @chmod($preCommit, 0755);
}
