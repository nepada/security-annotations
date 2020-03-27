<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nette;

interface AccessValidator
{

    /**
     * @param mixed $annotation parsed value of annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess($annotation): void;

}