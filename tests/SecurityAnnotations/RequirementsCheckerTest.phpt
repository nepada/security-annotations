<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Mockery\MockInterface;
use Nepada\SecurityAnnotations;
use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;
use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nepada\SecurityAnnotations\Annotations\LoggedIn;
use Nepada\SecurityAnnotations\Annotations\Role;
use NepadaTests\SecurityAnnotations\Fixtures\TestAnnotationsPresenter;
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
                $this->createRequirementsChecker($this->mockAccessValidator(self::class), $this->mockAccessValidator($lowerCaseClass));
            },
            \LogicException::class,
            'Access validator for annotation NepadaTests\SecurityAnnotations\RequirementsCheckerTest is already registered.',
        );
    }

    public function testProtectElement(): void
    {
        $loggedInValidator = $this->mockAccessValidator(LoggedIn::class);
        $loggedInValidator->shouldReceive('validateAccess')->withArgs([LoggedIn::class])->once();

        $roleValidator = $this->mockAccessValidator(Role::class);
        $roleValidator->shouldReceive('validateAccess')->withArgs(
            function (Role $annotation): bool {
                Assert::same(['a', 'b', 'c'], $annotation->roles);
                return true;
            },
        )->once();
        $roleValidator->shouldReceive('validateAccess')->withArgs(
            function (Role $annotation): bool {
                Assert::same(['d'], $annotation->roles);
                return true;
            },
        )->once();

        $allowedValidator = $this->mockAccessValidator(Allowed::class);
        $allowedValidator->shouldReceive('validateAccess')->withArgs(
            function (Allowed $annotation): bool {
                Assert::same('foo', $annotation->resource);
                Assert::same('bar', $annotation->privilege);
                return true;
            },
        )->once();

        $requirementsChecker = $this->createRequirementsChecker($loggedInValidator, $roleValidator, $allowedValidator);

        Assert::noError(function () use ($requirementsChecker): void {
            $requirementsChecker->protectElement(new \ReflectionClass(TestAnnotationsPresenter::class));
        });
    }

    /**
     * @template T of object
     * @phpstan-param class-string<T> $annotationName
     * @param string $annotationName
     * @return AccessValidator<T>|MockInterface
     */
    private function mockAccessValidator(string $annotationName): AccessValidator
    {
        $mock = \Mockery::mock(AccessValidator::class);
        $mock->shouldReceive('getSupportedAnnotationName')->andReturn($annotationName);

        return $mock;
    }

    private function createRequirementsChecker(AccessValidator ...$accessValidators): SecurityAnnotations\RequirementsChecker
    {
        return new SecurityAnnotations\RequirementsChecker(new AnnotationReader(new DocParser()), ...$accessValidators);
    }

}


(new RequirementsCheckerTest())->run();
