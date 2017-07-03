<?php
/**
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nepada\SecurityAnnotations\UnexpectedValueException;
use Nette;
use Nette\Security\User;


class LoggedInValidator implements IAccessValidator
{

    use Nette\SmartObject;

    /** @var User */
    private $user;


    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param mixed $annotation parsed value of annotation
     * @throws Nette\Application\BadRequestException
     */
    public function validateAccess($annotation): void
    {
        if (!is_bool($annotation)) {
            throw new UnexpectedValueException('Unexpected annotation type, bool expected.');
        }

        if ($annotation && !$this->user->isLoggedIn()) {
            throw new Nette\Application\ForbiddenRequestException('User is not logged in.');
        }
    }

}
