<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;

$config = ['parameters' => ['ignoreErrors' => []]];

if (InstalledVersions::satisfies(new VersionParser(), 'nette/security', '<3.2.3')) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(mixed\\): mixed\\)|null, Closure\\(Nette\\\\Security\\\\Role\\|string\\): string given\\.$#',
        'path' => __DIR__ . '/../../src/SecurityAnnotations/AccessValidators/RoleValidator.php',
        'count' => 1,
    ];
}

return $config;
