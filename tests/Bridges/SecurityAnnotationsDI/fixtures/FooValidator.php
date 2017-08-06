<?php
/**
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace NepadaTests\Bridges\SecurityAnnotationsDI;

use Nepada\SecurityAnnotations\AccessValidators\IAccessValidator;
use Nette;


class FooValidator implements IAccessValidator
{

    /**
     * @param mixed $annotation parsed value of annotation
     * @throws Nette\Application\BadRequestException
     */
    public function validateAccess($annotation): void
    {
    }

}
