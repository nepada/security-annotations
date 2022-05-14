<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AccessValidators;

use Mockery;
use Mockery\MockInterface;
use Nepada\SecurityAnnotations\AccessValidators;
use Nepada\SecurityAnnotations\Annotations\LoggedIn;
use NepadaTests\TestCase;
use Nette;
use Nette\Security\User;
use Tester\Assert;
use Tester\Environment;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class LoggedInValidatorTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        Environment::bypassFinals();
    }

    public function testAccessAllowed(): void
    {
        $annotation = new LoggedIn();
        $user = $this->mockUser(true);
        $validator = new AccessValidators\LoggedInValidator($user);

        Assert::noError(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        });
    }

    public function testAccessDenied(): void
    {
        $annotation = new LoggedIn();
        $user = $this->mockUser(false);
        $validator = new AccessValidators\LoggedInValidator($user);

        Assert::exception(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        }, Nette\Application\ForbiddenRequestException::class);
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
