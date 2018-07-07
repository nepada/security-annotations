<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\SecurityAnnotationsDI;

use Nepada\SecurityAnnotations\AccessValidators\IAccessValidator;

class BarValidator implements IAccessValidator
{

    /**
     * @param mixed $annotation parsed value of annotation
     */
    public function validateAccess($annotation): void
    {
    }

}
