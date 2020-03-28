<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nette;
use Nette\Security\IAuthorizator;
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
     * @phpstan-param Allowed $annotation
     * @param object|Allowed $annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess(object $annotation): void
    {
        $resource = $annotation->resource ?? IAuthorizator::ALL;
        $privilege = $annotation->privilege ?? IAuthorizator::ALL;
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
