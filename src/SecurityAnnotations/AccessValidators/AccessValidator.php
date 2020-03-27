<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nette;

/**
 * @template TAnnotation of object
 */
interface AccessValidator
{

    /**
     * @phpstan-return class-string<TAnnotation>
     */
    public function getSupportedAnnotationName(): string;

    /**
     * @phpstan-param TAnnotation $annotation
     * @param object $annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess(object $annotation): void;

}
