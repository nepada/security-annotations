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
        $requirementsChecker = $this->createRequirementsChecker();
        $requirementsChecker->addAccessValidator($this->mockAccessValidator(self::class));

        Assert::exception(
            function () use ($requirementsChecker): void {
                /** @var class-string<object> $lowerCaseClass */
                $lowerCaseClass = Strings::lower(self::class);
                $requirementsChecker->addAccessValidator($this->mockAccessValidator($lowerCaseClass));
            },
            \LogicException::class,
            'Access validator for annotation NepadaTests\SecurityAnnotations\RequirementsCheckerTest is already registered.',
        );
    }

    public function testProtectElement(): void
    {
        $requirementsChecker = $this->createRequirementsChecker();

        $loggedInValidator = $this->mockAccessValidator(LoggedIn::class);
        $loggedInValidator->shouldReceive('validateAccess')->withArgs([LoggedIn::class])->once();
        $requirementsChecker->addAccessValidator($loggedInValidator);

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
        $requirementsChecker->addAccessValidator($roleValidator);

        $allowedValidator = $this->mockAccessValidator(Allowed::class);
        $allowedValidator->shouldReceive('validateAccess')->withArgs(
            function (Allowed $annotation): bool {
                Assert::same('foo', $annotation->resource);
                Assert::same('bar', $annotation->privilege);
                return true;
            },
        )->once();
        $requirementsChecker->addAccessValidator($allowedValidator);

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

    private function createRequirementsChecker(): SecurityAnnotations\RequirementsChecker
    {
        return new SecurityAnnotations\RequirementsChecker(new AnnotationReader(new DocParser()));
    }

}


(new RequirementsCheckerTest())->run();
