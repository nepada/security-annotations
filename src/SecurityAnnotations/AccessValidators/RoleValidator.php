<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nepada\SecurityAnnotations\Annotations\Role;
use Nette;
use Nette\Security\Permission;
use Nette\Security\User;

/**
 * @implements AccessValidator<Role>
 */
class RoleValidator implements AccessValidator
{

    use Nette\SmartObject;

    private User $user;

    private ?Permission $permission = null;

    public function __construct(User $user, ?Permission $permission = null)
    {
        $this->user = $user;
        $this->permission = $permission;
    }

    public function getSupportedAnnotationName(): string
    {
        return Role::class;
    }

    /**
     * @phpstan-param Role $annotation
     * @param object|Role $annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess(object $annotation): void
    {
        $allowedRoles = $annotation->roles;
        $userRoles = $this->user->getRoles();

        if (array_intersect($userRoles, $allowedRoles) !== []) {
            return;
        }

        if ($this->permission !== null) {
            foreach ($userRoles as $userRole) {
                foreach ($allowedRoles as $allowedRole) {
                    try {
                        if ($this->permission->roleInheritsFrom($userRole, $allowedRole)) {
                            return;
                        }
                    } catch (Nette\InvalidStateException $exception) {
                        // ignore undefined roles
                    }
                }
            }
        }

        $message = sprintf("User is not in any of the required roles '%s'.", implode("', '", $annotation->roles));
        throw new Nette\Application\ForbiddenRequestException($message);
    }

}
