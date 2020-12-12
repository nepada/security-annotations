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
     * @internal use getter instead
     * @var string
     */
    public ?string $resource;

    /**
     * @internal use getter instead
     * @var string
     */
    public ?string $privilege;

    public function __construct(?string $resource = Nette\Security\IAuthorizator::ALL, ?string $privilege = Nette\Security\IAuthorizator::ALL)
    {
        $this->resource = $resource;
        $this->privilege = $privilege;
    }

    public function getResource(): ?string
    {
        return $this->resource;
    }

    public function getPrivilege(): ?string
    {
        return $this->privilege;
    }

}
