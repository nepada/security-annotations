<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AccessValidators;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use Mockery;
use Mockery\MockInterface;
use Nepada\SecurityAnnotations\AccessValidators;
use Nepada\SecurityAnnotations\Annotations\Role;
use NepadaTests\TestCase;
use Nette;
use Nette\Security\User;
use Nette\Utils\Arrays;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class RoleValidatorTest extends TestCase
{

    private DocParser $docParser;

    protected function setUp(): void
    {
        parent::setUp();
        AnnotationRegistry::registerUniqueLoader('class_exists');
        $this->docParser = new DocParser();
    }

    /**
     * @dataProvider getDataForAccessAllowed
     * @param string $input
     * @param string[] $userRoles
     * @param string[]|null $rolesInheritance
     */
    public function testAccessAllowed(string $input, array $userRoles, ?array $rolesInheritance): void
    {
        $annotation = $this->parseAnnotation($input);
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
    protected function getDataForAccessAllowed(): array
    {
        return [
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Role("bar")',
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => null,
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Role({"xyz", "bar", "abc"})',
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => null,
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Role({"abc", "cde"})',
                'userRoles' => ['foo', 'bar', 'baz'],
                'rolesInheritance' => ['foo' => ['xyz'], 'bar' => ['abc']],
            ],
        ];
    }

    /**
     * @dataProvider getDataForAccessDenied
     * @param string $input
     * @param string[] $userRoles
     * @param string[]|null $rolesInheritance
     */
    public function testAccessDenied(string $input, array $userRoles, ?array $rolesInheritance): void
    {
        $annotation = $this->parseAnnotation($input);
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
    protected function getDataForAccessDenied(): array
    {
        return [
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Role("required")',
                'userRoles' => [],
                'rolesInheritance' => null,
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Role({"required", "another"})',
                'userRoles' => ['baz'],
                'rolesInheritance' => null,
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Role({"required", "another"})',
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
     * @param string[] $rolesInheritance
     * @return Nette\Security\Permission|MockInterface
     */
    private function mockPermission(array $rolesInheritance): Nette\Security\Permission
    {
        $permission = Mockery::mock(Nette\Security\Permission::class);
        $permission->shouldReceive('roleInheritsFrom')->andReturnUsing(
            fn (string $child, string $parent): bool => in_array($parent, Arrays::get($rolesInheritance, $child, []), true),
        );

        return $permission;
    }

    private function parseAnnotation(string $input): Role
    {
        $annotations = $this->docParser->parse($input);
        Assert::count(1, $annotations);
        Assert::type(Role::class, $annotations[0]);
        return $annotations[0];
    }

}


(new RoleValidatorTest())->run();
