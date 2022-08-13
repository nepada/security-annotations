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
     * @var non-empty-list<string>
     */
    private array $roles;

    public function __construct(string ...$roles)
    {
        if ($roles === []) {
            throw new \InvalidArgumentException('At least one role name must be specified');
        }
        $this->roles = array_values($roles);
    }

    /**
     * @return non-empty-list<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

}
