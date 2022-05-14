<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AccessValidators;

use Mockery;
use Mockery\MockInterface;
use Nepada\SecurityAnnotations\AccessValidators;
use Nepada\SecurityAnnotations\Annotations\Allowed;
use NepadaTests\TestCase;
use Nette;
use Nette\Security\IAuthorizator;
use Nette\Security\User;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class PermissionValidatorTest extends TestCase
{

    /**
     * @dataProvider getDataForAccessAllowed
     * @param Allowed $annotation
     * @param string|null $resource
     * @param string|null $privilege
     */
    public function testAccessAllowed(Allowed $annotation, ?string $resource, ?string $privilege): void
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
    protected function getDataForAccessAllowed(): array
    {
        return [
            [
                'annotation' => new Allowed(),
                'resource' => IAuthorizator::ALL,
                'privilege' => IAuthorizator::ALL,
            ],
            [
                'annotation' => new Allowed('foo'),
                'resource' => 'foo',
                'privilege' => IAuthorizator::ALL,
            ],
            [
                'annotation' => new Allowed(null, 'edit'),
                'resource' => IAuthorizator::ALL,
                'privilege' => 'edit',
            ],
            [
                'annotation' => new Allowed('foo', 'edit'),
                'resource' => 'foo',
                'privilege' => 'edit',
            ],
        ];
    }

    /**
     * @dataProvider getDataForAccessDenied
     * @param Allowed $annotation
     * @param string|null $resource
     * @param string|null $privilege
     * @param string $message
     */
    public function testAccessDenied(Allowed $annotation, ?string $resource, ?string $privilege, string $message): void
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
    protected function getDataForAccessDenied(): array
    {
        return [
            [
                'annotation' => new Allowed(),
                'resource' => IAuthorizator::ALL,
                'privilege' => IAuthorizator::ALL,
                'message' => 'User is not allowed to access the resource.',
            ],
            [
                'annotation' => new Allowed('foo'),
                'resource' => 'foo',
                'privilege' => IAuthorizator::ALL,
                'message' => "User is not allowed to access the resource 'foo'.",
            ],
            [
                'annotation' => new Allowed(null, 'edit'),
                'resource' => IAuthorizator::ALL,
                'privilege' => 'edit',
                'message' => 'User is not allowed to edit the resource.',
            ],
            [
                'annotation' => new Allowed('foo', 'edit'),
                'resource' => 'foo',
                'privilege' => 'edit',
                'message' => "User is not allowed to edit the resource 'foo'.",
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
