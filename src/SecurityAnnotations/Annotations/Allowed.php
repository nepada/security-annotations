<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Attribute;
use Nette;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Allowed
{

    use Nette\SmartObject;

    public function __construct(
        public readonly Nette\Security\Resource|string|null $resource = Nette\Security\Authorizator::ALL,
        public readonly ?string $privilege = Nette\Security\Authorizator::ALL,
    )
    {
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getResource(): Nette\Security\Resource|string|null
    {
        return $this->resource;
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getPrivilege(): ?string
    {
        return $this->privilege;
    }

}
