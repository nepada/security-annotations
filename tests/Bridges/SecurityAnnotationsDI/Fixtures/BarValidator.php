<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\SecurityAnnotationsDI\Fixtures;

use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;

class BarValidator implements AccessValidator
{

    /**
     * @param mixed $annotation parsed value of annotation
     */
    public function validateAccess($annotation): void
    {
    }

}
