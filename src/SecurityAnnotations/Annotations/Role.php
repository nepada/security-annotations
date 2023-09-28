<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Attribute;
use Nette;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Role
{

    use Nette\SmartObject;

    /**
     * @var non-empty-list<Nette\Security\Role|string>
     */
    public readonly array $roles;

    public function __construct(Nette\Security\Role|string ...$roles)
    {
        if ($roles === []) {
            throw new \InvalidArgumentException('At least one role name must be specified');
        }
        $this->roles = array_values($roles);
    }

    /**
     * @deprecated read the property directly instead
     * @return non-empty-list<Nette\Security\Role|string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

}
