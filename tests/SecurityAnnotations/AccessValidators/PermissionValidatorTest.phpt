<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AccessValidators;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
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
     * @param string|null $resource
     * @param string|null $privilege
     */
    public function testAccessAllowed(string $input, ?string $resource, ?string $privilege): void
    {
        $annotation = $this->parseAnnotation($input);
        var_dump($annotation);
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
                'input' => '@Nepada\SecurityAnnotations\Annotations\Allowed()',
                'resource' => IAuthorizator::ALL,
                'privilege' => IAuthorizator::ALL,
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Allowed(resource="foo")',
                'resource' => 'foo',
                'privilege' => IAuthorizator::ALL,
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Allowed(privilege="edit")',
                'resource' => IAuthorizator::ALL,
                'privilege' => 'edit',
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Allowed(resource="foo", privilege="edit")',
                'resource' => 'foo',
                'privilege' => 'edit',
            ],
        ];
    }

    /**
     * @dataProvider getDataForAccessDenied
     * @param string $input
     * @param string|null $resource
     * @param string|null $privilege
     * @param string $message
     */
    public function testAccessDenied(string $input, ?string $resource, ?string $privilege, string $message): void
    {
        $annotation = $this->parseAnnotation($input);
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
                'input' => '@Nepada\SecurityAnnotations\Annotations\Allowed()',
                'resource' => IAuthorizator::ALL,
                'privilege' => IAuthorizator::ALL,
                'message' => 'User is not allowed to access the resource.',
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Allowed(resource="foo")',
                'resource' => 'foo',
                'privilege' => IAuthorizator::ALL,
                'message' => "User is not allowed to access the resource 'foo'.",
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Allowed(privilege="edit")',
                'resource' => IAuthorizator::ALL,
                'privilege' => 'edit',
                'message' => 'User is not allowed to edit the resource.',
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Allowed(resource="foo", privilege="edit")',
                'resource' => 'foo',
                'privilege' => 'edit',
                'message' => "User is not allowed to edit the resource 'foo'.",
            ],
            [
                'input' => '@Nepada\SecurityAnnotations\Annotations\Allowed(resource="foo", privilege="edit", message="Custom error message.")',
                'resource' => 'foo',
                'privilege' => 'edit',
                'message' => 'Custom error message.',
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

    private function parseAnnotation(string $input): Allowed
    {
        $annotations = $this->docParser->parse($input);
        Assert::count(1, $annotations);
        Assert::type(Allowed::class, $annotations[0]);
        return $annotations[0];
    }

}


(new PermissionValidatorTest())->run();
