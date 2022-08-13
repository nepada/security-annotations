<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Attribute;
use Nette;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Allowed
{

    use Nette\SmartObject;

    private Nette\Security\Resource|string|null $resource;

    private ?string $privilege;

    public function __construct(Nette\Security\Resource|string|null $resource = Nette\Security\Authorizator::ALL, ?string $privilege = Nette\Security\Authorizator::ALL)
    {
        $this->resource = $resource;
        $this->privilege = $privilege;
    }

    public function getResource(): Nette\Security\Resource|string|null
    {
        return $this->resource;
    }

    public function getPrivilege(): ?string
    {
        return $this->privilege;
    }

}
