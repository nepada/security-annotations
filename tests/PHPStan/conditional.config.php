<?php
declare(strict_types = 1);

$config = [];

if (PHP_VERSION_ID < 8_00_00) {
    // Attributes are not supported on PHP <8.0
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to an undefined method ReflectionClass\\|ReflectionMethod\\:\\:getAttributes\\(\\)\\.$#',
        'path' => '../../src/SecurityAnnotations/AnnotationReaders/AttributesReader.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to method newInstance\\(\\) on an unknown class ReflectionAttribute\\.$#',
        'path' => '../../src/SecurityAnnotations/AnnotationReaders/AttributesReader.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Parameter \\$attribute of anonymous function has invalid typehint type ReflectionAttribute\\.$#',
        'path' => '../../src/SecurityAnnotations/AnnotationReaders/AttributesReader.php',
        'count' => 1,
    ];
}

return $config;
