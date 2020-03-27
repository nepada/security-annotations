<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AccessValidators;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
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

    private DocParser $docParser;

    protected function setUp(): void
    {
        parent::setUp();
        Environment::bypassFinals();
        AnnotationRegistry::registerUniqueLoader('class_exists');
        $this->docParser = new DocParser();
    }

    public function testAccessAllowed(): void
    {
        $annotation = $this->parseAnnotation('@Nepada\SecurityAnnotations\Annotations\LoggedIn');
        $user = $this->mockUser(true);
        $validator = new AccessValidators\LoggedInValidator($user);

        Assert::noError(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        });
    }

    public function testAccessDenied(): void
    {
        $annotation = $this->parseAnnotation('@Nepada\SecurityAnnotations\Annotations\LoggedIn');
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

    private function parseAnnotation(string $input): LoggedIn
    {
        $annotations = $this->docParser->parse($input);
        Assert::count(1, $annotations);
        Assert::type(LoggedIn::class, $annotations[0]);
        return $annotations[0];
    }

}


(new LoggedInValidatorTest())->run();
