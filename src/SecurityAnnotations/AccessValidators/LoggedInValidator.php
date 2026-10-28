<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nepada\SecurityAnnotations\Annotations\LoggedIn;
use Nette;
use Nette\Security\User;

/**
 * @implements AccessValidator<LoggedIn>
 */
class LoggedInValidator implements AccessValidator
{

    use Nette\SmartObject;

    public function __construct(
        private User $user,
    )
    {
    }

    public function getSupportedAnnotationName(): string
    {
        return LoggedIn::class;
    }

    /**
     * @param LoggedIn $annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess(object $annotation): void
    {
        if ($this->user->isLoggedIn()) {
            return;
        }

        throw new Nette\Application\ForbiddenRequestException('User is not logged in.');
    }

}
