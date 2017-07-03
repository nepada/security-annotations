<?php
/**
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nepada\SecurityAnnotations\UnexpectedValueException;
use Nette;
use Nette\Security\IAuthorizator;
use Nette\Security\User;


class PermissionValidator implements IAccessValidator
{

    use Nette\SmartObject;

    private const RESOURCE = 'resource';
    private const PRIVILEGE = 'privilege';
    private const MESSAGE = 'message';

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
        if ($annotation instanceof \Traversable) {
            $annotation = iterator_to_array($annotation);
        } elseif (!is_array($annotation)) {
            throw new UnexpectedValueException('Unexpected annotation type, array or Traversable expected.');
        }

        $resource = $annotation[self::RESOURCE] ?? IAuthorizator::ALL;
        $privilege = $annotation[self::PRIVILEGE] ?? IAuthorizator::ALL;

        if (!$this->user->isAllowed($resource, $privilege)) {
            if (isset($annotation[self::MESSAGE])) {
                $message = $annotation['message'];
            } else {
                $message = sprintf(
                    'User is not allowed to %s the resource%s.',
                    $privilege ?? 'access',
                    $resource !== null ? " '$resource'" : ''
                );
            }
            throw new Nette\Application\ForbiddenRequestException($message);
        }
    }

}
