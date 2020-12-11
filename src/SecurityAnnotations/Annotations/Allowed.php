<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Nette;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
final class Allowed
{

    use Nette\SmartObject;

    /**
     * @var string
     */
    public ?string $resource = null;

    /**
     * @var string
     */
    public ?string $privilege = null;

}
