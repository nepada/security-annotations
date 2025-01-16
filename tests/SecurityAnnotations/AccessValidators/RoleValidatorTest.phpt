<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AccessValidators;

use Mockery;
use Mockery\MockInterface;
use Nepada\SecurityAnnotations\AccessValidators;
use Nepada\SecurityAnnotations\Annotations\Role;
use NepadaTests\SecurityAnnotations\Fixtures\BarRole;
use NepadaTests\TestCase;
use Nette;
use Nette\Security\User;
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
     * @param array<array<string>>|null $rolesInheritance
     */
    public function testAccessAllowed(Role $annotation, array $userRoles, ?array $rolesInheritance): void
    {
        $user = $this->mockUser($userRoles);
        $permission = $rolesInheritance === null ? null : $this->mockPermission($rolesInheritance);
        $validator = new AccessValidators\RoleValidator($user, $permission);

        Assert::noError(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        });
    }

    /**
     * @return list<mixed[]>
     */
    protected function getDataForAccessAllowed(): array
    {
        return [
            [
                'annotation' => new Role(new BarRole()),
                'userRoles' => ['foo', new BarRole(), 'baz'],
                'rolesInheritance' => null,
            ],
            [
                'annotation' => new Role('xyz', 'bar', 'abc'),
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => null,
            ],
            [
                'annotation' => new Role('abc', 'cde'),
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => ['foo' => ['xyz'], 'bar' => ['abc']],
            ],
        ];
    }

    /**
     * @dataProvider getDataForAccessDenied
     * @param string[] $userRoles
     * @param array<array<string>>|null $rolesInheritance
     */
    public function testAccessDenied(Role $annotation, array $userRoles, ?array $rolesInheritance): void
    {
        $user = $this->mockUser($userRoles);
        $permission = $rolesInheritance === null ? null : $this->mockPermission($rolesInheritance);
        $validator = new AccessValidators\RoleValidator($user, $permission);

        Assert::exception(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        }, Nette\Application\ForbiddenRequestException::class);
    }

    /**
     * @return list<mixed[]>
     */
    protected function getDataForAccessDenied(): array
    {
        return [
            [
                'annotation' => new Role('required'),
                'userRoles' => [],
                'rolesInheritance' => null,
            ],
            [
                'annotation' => new Role('required', 'another'),
                'userRoles' => ['baz'],
                'rolesInheritance' => null,
            ],
            [
                'annotation' => new Role('required', 'another'),
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => ['foo' => ['xyz'], 'bar' => ['abc']],
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
     * @param array<array<string>> $rolesInheritance
     * @return Nette\Security\Permission|MockInterface
     */
    private function mockPermission(array $rolesInheritance): Nette\Security\Permission
    {
        $permission = Mockery::mock(Nette\Security\Permission::class);
        $permission->shouldReceive('roleInheritsFrom')->andReturnUsing(
            fn (string $child, string $parent): bool => in_array($parent, $rolesInheritance[$child] ?? [], true),
        );

        return $permission;
    }

}


(new RoleValidatorTest())->run();
