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
final class Role
{

    use Nette\SmartObject;

    /**
     * @var non-empty-list<string>
     */
    private array $roles;

    /**
     * @param array{value: string|non-empty-list<string>}|non-empty-list<string>|string ...$roles
     */
    public function __construct(string|array ...$roles)
    {
        if (isset($roles[0]['value'])) { // Compatibility with Doctrine annotations
            $roles = is_string($roles[0]['value']) ? [$roles[0]['value']] : $roles[0]['value'];
        } elseif (func_num_args() === 1 && is_array(func_get_arg(0))) { // BC with passing roles as an array
            trigger_error('Passing roles as a single array argument is deprecated, use variadic argument instead', E_USER_DEPRECATED);
            $roles = func_get_arg(0);
        }
        if ($roles === []) {
            throw new \InvalidArgumentException('At least one role name must be specified');
        }
        foreach ($roles as $role) {
            if (! is_string($role)) {
                $type = gettype($role);
                throw new \InvalidArgumentException("Expected string with role name, got {$type}");
            }
        }

        $this->roles = $roles;
    }

    /**
     * @return non-empty-list<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

}
