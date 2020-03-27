<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\SecurityAnnotationsDI\Fixtures;

use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;

class FooValidator implements AccessValidator
{

    /**
     * @param mixed $annotation parsed value of annotation
     */
    public function validateAccess($annotation): void
    {
    }

}
