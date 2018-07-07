<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AccessValidators;

use Mockery;
use Mockery\MockInterface;
use Nepada;
use Nepada\SecurityAnnotations\AccessValidators;
use NepadaTests\TestCase;
use Nette;
use Nette\Security\User;
use Nette\Utils\ArrayHash;
use Nette\Utils\Arrays;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class RoleValidatorTest extends TestCase
{

    /**
     * @dataProvider getDataForAccessAllowed
     * @param string[] $userRoles
     * @param string[]|null $rolesInheritance
     * @param string|string[] $annotation
     */
    public function testAccessAllowed(array $userRoles, ?array $rolesInheritance, $annotation): void
    {
        $user = $this->mockUser($userRoles);
        $permission = $rolesInheritance === null ? null : $this->mockPermission($rolesInheritance);
        $validator = new AccessValidators\RoleValidator($user, $permission);

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
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => null,
                'annotation' => 'bar',
            ],
            [
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => null,
                'annotation' => ['xyz', 'bar', 'abc'],
            ],
            [
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => ['foo' => ['xyz'], 'bar' => ['abc']],
                'annotation' => ArrayHash::from(['abc', 'cde']),
            ],
        ];
    }

    /**
     * @dataProvider getDataForAccessDenied
     * @param string[] $userRoles
     * @param string[]|null $rolesInheritance
     * @param string|string[] $annotation
     */
    public function testAccessDenied(array $userRoles, ?array $rolesInheritance, $annotation): void
    {
        $user = $this->mockUser($userRoles);
        $permission = $rolesInheritance === null ? null : $this->mockPermission($rolesInheritance);
        $validator = new AccessValidators\RoleValidator($user, $permission);

        Assert::exception(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        }, Nette\Application\ForbiddenRequestException::class);
    }

    /**
     * @return mixed[]
     */
    public function getDataForAccessDenied(): array
    {
        return [
            [
                'userRoles' => [],
                'rolesInheritance' => null,
                'annotation' => 'required',
            ],
            [
                'userRoles' => ['baz'],
                'rolesInheritance' => null,
                'annotation' => ['required', 'another'],
            ],
            [
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => ['foo' => ['xyz'], 'bar' => ['abc']],
                'annotation' => ArrayHash::from(['required', 'another']),
            ],
        ];
    }

    /**
     * @dataProvider getDataForInvalidAnnotation
     * @param mixed $annotation
     */
    public function testInvalidAnnotation($annotation): void
    {
        $user = $this->mockUser();
        $validator = new AccessValidators\RoleValidator($user);

        Assert::exception(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        }, \InvalidArgumentException::class, 'Unexpected annotation type, string or a list of strings expected.');
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
                'annotation' => ArrayHash::from(['foo' => 'bar']),
            ],
            [
                'annotation' => ['foo', 42],
            ],
        ];
    }

    /**
     * @param string[] $roles
     * @return User|MockInterface
     */
    private function mockUser(array $roles = []): User
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getRoles')->andReturn($roles);

        return $user;
    }

    /**
     * @param string[] $rolesInheritance
     * @return Nette\Security\Permission|MockInterface
     */
    private function mockPermission(array $rolesInheritance): Nette\Security\Permission
    {
        $permission = Mockery::mock(Nette\Security\Permission::class);
        $permission->shouldReceive('roleInheritsFrom')->andReturnUsing(
            function (string $child, string $parent) use ($rolesInheritance): bool {
                return in_array($parent, Arrays::get($rolesInheritance, $child, []), true);
            }
        );

        return $permission;
    }

}


(new RoleValidatorTest())->run();
