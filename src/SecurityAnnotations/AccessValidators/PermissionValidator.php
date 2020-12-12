<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nette;
use Nette\Security\User;

/**
 * @implements AccessValidator<Allowed>
 */
class PermissionValidator implements AccessValidator
{

    use Nette\SmartObject;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getSupportedAnnotationName(): string
    {
        return Allowed::class;
    }

    /**
     * @param Allowed $annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess(object $annotation): void
    {
        $resource = $annotation->getResource();
        $privilege = $annotation->getPrivilege();
        if ($this->user->isAllowed($resource, $privilege)) {
            return;
        }

        $message = sprintf(
            'User is not allowed to %s the resource%s.',
            $privilege ?? 'access',
            $resource !== null ? " '$resource'" : '',
        );
        throw new Nette\Application\ForbiddenRequestException($message);
    }

}
