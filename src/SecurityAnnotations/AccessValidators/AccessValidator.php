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
     * @return class-string<TAnnotation>
     */
    public function getSupportedAnnotationName(): string;

    /**
     * @param TAnnotation $annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess(object $annotation): void;

}
