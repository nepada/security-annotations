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
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class LoggedInValidatorTest extends TestCase
{

    /**
     * @dataProvider getDataForAccessAllowed
     * @param bool $isLoggedIn
     * @param bool $annotation
     */
    public function testAccessAllowed(bool $isLoggedIn, bool $annotation): void
    {
        $user = $this->mockUser($isLoggedIn);
        $validator = new AccessValidators\LoggedInValidator($user);

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
                'isLoggedIn' => true,
                'annotation' => true,
            ],
            [
                'isLoggedIn' => true,
                'annotation' => false,
            ],
            [
                'isLoggedIn' => false,
                'annotation' => false,
            ],
        ];
    }

    public function testAccessDenied(): void
    {
        $user = $this->mockUser(false);
        $validator = new AccessValidators\LoggedInValidator($user);

        Assert::exception(function () use ($validator): void {
            $validator->validateAccess(true);
        }, Nette\Application\ForbiddenRequestException::class);
    }

    /**
     * @dataProvider getDataForInvalidAnnotation
     * @param mixed $annotation
     */
    public function testInvalidAnnotation($annotation): void
    {
        $user = $this->mockUser();
        $validator = new AccessValidators\LoggedInValidator($user);

        Assert::exception(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        }, \InvalidArgumentException::class, 'Unexpected annotation type, bool expected.');
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
                'annotation' => 'foo',
            ],
            [
                'annotation' => ['foo', 'bar'],
            ],
            [
                'annotation' => ArrayHash::from(['foo' => 'bar']),
            ],
        ];
    }

    /**
     * @param bool $isLoggedIn
     * @return User|MockInterface
     */
    private function mockUser(bool $isLoggedIn = false): User
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isLoggedIn')->andReturn($isLoggedIn);

        return $user;
    }

}


(new LoggedInValidatorTest())->run();
