<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Mockery\MockInterface;
use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;
use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nepada\SecurityAnnotations\Annotations\LoggedIn;
use Nepada\SecurityAnnotations\Annotations\Role;
use Nepada\SecurityAnnotations\RequirementsChecker;
use NepadaTests\SecurityAnnotations\AnnotationReaders\DummyAnnotationReader;
use NepadaTests\TestCase;
use Nette\Utils\Strings;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class RequirementsCheckerTest extends TestCase
{

    public function testAddDuplicateAccessValidator(): void
    {
        Assert::exception(
            function (): void {
                /** @var class-string<object> $lowerCaseClass */
                $lowerCaseClass = Strings::lower(self::class);
                $requirementsChecker = new RequirementsChecker(
                    new DummyAnnotationReader([]),
                    $this->mockAccessValidator(self::class),
                    $this->mockAccessValidator($lowerCaseClass),
                );
            },
            \LogicException::class,
            'Access validator for annotation NepadaTests\SecurityAnnotations\RequirementsCheckerTest is already registered.',
        );
    }

    public function testProtectElement(): void
    {
        $loggedIn = new LoggedIn();
        $role1 = new Role('foo');
        $role2 = new Role(['a', 'b']);
        $allowed = new Allowed();
        $annotationsReader = new DummyAnnotationReader([$loggedIn, $role1, $role2, $allowed]);

        $loggedInValidator = $this->mockAccessValidator(LoggedIn::class);
        $loggedInValidator->shouldReceive('validateAccess')->withArgs([$loggedIn])->once();

        $roleValidator = $this->mockAccessValidator(Role::class);
        $roleValidator->shouldReceive('validateAccess')->withArgs([$role1])->once();
        $roleValidator->shouldReceive('validateAccess')->withArgs([$role2])->once();

        $requirementsChecker = new RequirementsChecker($annotationsReader, $loggedInValidator, $roleValidator);

        Assert::noError(function () use ($requirementsChecker): void {
            $requirementsChecker->protectElement(\Mockery::mock(\ReflectionClass::class));
        });
    }

    /**
     * @template T of object
     * @param class-string<T> $annotationName
     * @return AccessValidator<T>|MockInterface
     */
    private function mockAccessValidator(string $annotationName): AccessValidator
    {
        $mock = \Mockery::mock(AccessValidator::class);
        $mock->shouldReceive('getSupportedAnnotationName')->andReturn($annotationName);

        return $mock;
    }

}


(new RequirementsCheckerTest())->run();
