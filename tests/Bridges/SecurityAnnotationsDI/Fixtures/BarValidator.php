<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\SecurityAnnotationsDI\Fixtures;

use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;

/**
 * @implements AccessValidator<BarValidator>
 */
class BarValidator implements AccessValidator
{

    public function getSupportedAnnotationName(): string
    {
        return static::class;
    }

    /**
     * @param mixed $annotation parsed value of annotation
     */
    public function validateAccess($annotation): void
    {
    }

}
