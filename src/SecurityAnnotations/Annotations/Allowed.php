<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Attribute;
use Nette;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Allowed
{

    use Nette\SmartObject;

    public readonly Nette\Security\Resource|string|null $resource;

    public readonly ?string $privilege;

    public function __construct(Nette\Security\Resource|string|null $resource = Nette\Security\Authorizator::ALL, ?string $privilege = Nette\Security\Authorizator::ALL)
    {
        $this->resource = $resource;
        $this->privilege = $privilege;
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
