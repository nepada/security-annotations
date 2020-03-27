<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nette;
use Nette\Security\User;

class LoggedInValidator implements AccessValidator
{

    use Nette\SmartObject;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getSupportedAnnotationName(): string
    {
        return 'loggedIn';
    }

    /**
     * @param mixed $annotation parsed value of annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess($annotation): void
    {
        if (! is_bool($annotation)) {
            throw new \InvalidArgumentException('Unexpected annotation type, bool expected.');
        }

        if ($annotation && ! $this->user->isLoggedIn()) {
            throw new Nette\Application\ForbiddenRequestException('User is not logged in.');
        }
    }

}
