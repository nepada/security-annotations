<?php
/**
 * Test: Nepada\SecurityAnnotations\AccessValidators\PermissionValidator.
 *
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AccessValidators;

use Mockery;
use Mockery\MockInterface;
use Nepada;
use Nepada\SecurityAnnotations\AccessValidators;
use NepadaTests\TestCase;
use Nette;
use Nette\Security\IAuthorizator;
use Nette\Security\User;
use Nette\Utils\ArrayHash;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class PermissionValidatorTest extends TestCase
{

    /**
     * @dataProvider getDataForAccessAllowed
     *
     * @param string|null $resource
     * @param string|null $privilege
     * @param ArrayHash $annotation
     */
    public function testAccessAllowed(?string $resource, ?string $privilege, ArrayHash $annotation): void
    {
        $user = $this->mockUser($resource, $privilege, IAuthorizator::ALLOW);
        $validator = new AccessValidators\PermissionValidator($user);

        Assert::noError(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        });
    }

    /**
     * @return mixed[]
     */
    public function getDataForAccessAllowed(): array
    {
        return [
            [
                'resource' => IAuthorizator::ALL,
                'privilege' => IAuthorizator::ALL,
                'annotation' => ArrayHash::from([]),
            ],
            [
                'resource' => 'foo',
                'privilege' => IAuthorizator::ALL,
                'annotation' => ArrayHash::from(['resource' => 'foo']),
            ],
            [
                'resource' => IAuthorizator::ALL,
                'privilege' => 'edit',
                'annotation' => ArrayHash::from(['privilege' => 'edit']),
            ],
            [
                'resource' => 'foo',
                'privilege' => 'edit',
                'annotation' => ArrayHash::from(['resource' => 'foo', 'privilege' => 'edit']),
            ],
        ];
    }

    /**
     * @dataProvider getDataForAccessDenied
     *
     * @param string|null $resource
     * @param string|null $privilege
     * @param string $message
     * @param ArrayHash $annotation
     */
    public function testAccessDenied(?string $resource, ?string $privilege, string $message, ArrayHash $annotation): void
    {
        $user = $this->mockUser($resource, $privilege, IAuthorizator::DENY);
        $validator = new AccessValidators\PermissionValidator($user);

        Assert::exception(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        }, Nette\Application\ForbiddenRequestException::class, $message);
    }

    /**
     * @return mixed[]
     */
    public function getDataForAccessDenied(): array
    {
        return [
            [
                'resource' => IAuthorizator::ALL,
                'privilege' => IAuthorizator::ALL,
                'message' => 'User is not allowed to access the resource.',
                'annotation' => ArrayHash::from([]),
            ],
            [
                'resource' => 'foo',
                'privilege' => IAuthorizator::ALL,
                'message' => 'User is not allowed to access the resource \'foo\'.',
                'annotation' => ArrayHash::from(['resource' => 'foo']),
            ],
            [
                'resource' => IAuthorizator::ALL,
                'privilege' => 'edit',
                'message' => 'User is not allowed to edit the resource.',
                'annotation' => ArrayHash::from(['privilege' => 'edit']),
            ],
            [
                'resource' => 'foo',
                'privilege' => 'edit',
                'message' => 'User is not allowed to edit the resource \'foo\'.',
                'annotation' => ArrayHash::from(['resource' => 'foo', 'privilege' => 'edit']),
            ],
            [
                'resource' => 'foo',
                'privilege' => 'edit',
                'message' => 'Custom error message.',
                'annotation' => ArrayHash::from(
                    ['resource' => 'foo', 'privilege' => 'edit', 'message' => 'Custom error message.']
                ),
            ],
        ];
    }

    /**
     * @dataProvider getDataForInvalidAnnotation
     *
     * @param mixed $annotation
     */
    public function testInvalidAnnotation($annotation): void
    {
        $user = $this->mockUser();
        $validator = new AccessValidators\PermissionValidator($user);

        Assert::exception(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        }, Nepada\SecurityAnnotations\UnexpectedValueException::class, 'Unexpected annotation type, array or Traversable expected.');
    }

    /**
     * @return mixed[]
     */
    public function getDataForInvalidAnnotation(): array
    {
        return [
            [
                'annotation' => null,
            ],
            [
                'annotation' => true,
            ],
            [
                'annotation' => 42,
            ],
            [
                'annotation' => 'foo',
            ],
        ];
    }

    /**
     * @param string|null $resource
     * @param string|null $privilege
     * @param bool $isAllowed
     * @return User|MockInterface
     */
    private function mockUser(?string $resource = IAuthorizator::ALL, ?string $privilege = IAuthorizator::ALL, bool $isAllowed = false): User
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAllowed')->withArgs([$resource, $privilege])->andReturn($isAllowed);

        return $user;
    }

}


(new PermissionValidatorTest())->run();
