<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Attribute;
use Doctrine\Common\Annotations\NamedArgumentConstructorAnnotation;
use Nette;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Allowed implements NamedArgumentConstructorAnnotation
{

    use Nette\SmartObject;

    /**
     * @var string
     */
    public ?string $resource;

    /**
     * @var string
     */
    public ?string $privilege;

    public function __construct(?string $resource = null, ?string $privilege = null)
    {
        $this->resource = $resource;
        $this->privilege = $privilege;
    }

}
