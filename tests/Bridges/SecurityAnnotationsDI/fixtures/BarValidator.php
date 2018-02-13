<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\SecurityAnnotationsDI;

use Nepada\SecurityAnnotations\AccessValidators\IAccessValidator;
use Nette;


class BarValidator implements IAccessValidator
{

    /**
     * @param mixed $annotation parsed value of annotation
     * @throws Nette\Application\BadRequestException
     */
    public function validateAccess($annotation): void
    {
    }

}
