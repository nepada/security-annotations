<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nette;

interface AccessValidator
{

    public function getSupportedAnnotationName(): string;

    /**
     * @param mixed $annotation parsed value of annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess($annotation): void;

}
