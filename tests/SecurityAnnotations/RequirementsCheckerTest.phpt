<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Mockery\MockInterface;
use Nepada\SecurityAnnotations;
use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;
use NepadaTests\SecurityAnnotations\Fixtures\TestAnnotationsPresenter;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class RequirementsCheckerTest extends TestCase
{

    public function testAddDuplicateAccessValidator(): void
    {
        $requirementsChecker = new SecurityAnnotations\RequirementsChecker();
        $requirementsChecker->addAccessValidator($this->mockAccessValidator('duplicate'));

        Assert::exception(
            function () use ($requirementsChecker): void {
                $requirementsChecker->addAccessValidator($this->mockAccessValidator('duplicate'));
            },
            \LogicException::class,
            'Access validator for annotation "duplicate" is already registered.',
        );
    }

    public function testAddCaseInsensitiveDuplicateAccessValidator(): void
    {
        $requirementsChecker = new SecurityAnnotations\RequirementsChecker();
        $requirementsChecker->addAccessValidator($this->mockAccessValidator('duplicate'));

        Assert::exception(
            function () use ($requirementsChecker): void {
                $requirementsChecker->addAccessValidator($this->mockAccessValidator('DUPLICATE'));
            },
            \LogicException::class,
            'Access validator for annotation "DUPLICATE" is case insensitive match for already registered access validator "duplicate".',
        );
    }

    public function testProtectElement(): void
    {
        $expectArrayAnnotation = fn (array $expected): \Closure => fn ($annotation): bool => $this->matchArray($expected, $annotation);

        $requirementsChecker = new SecurityAnnotations\RequirementsChecker();

        $loggedInValidator = $this->mockAccessValidator('loggedIn');
        $loggedInValidator->shouldReceive('validateAccess')->withArgs([true])->once();
        $requirementsChecker->addAccessValidator($loggedInValidator);

        $roleValidator = $this->mockAccessValidator('role');
        $roleValidator->shouldReceive('validateAccess')->withArgs($expectArrayAnnotation(['a', 'b', 'c']))->once();
        $roleValidator->shouldReceive('validateAccess')->withArgs(['d'])->once();
        $requirementsChecker->addAccessValidator($roleValidator);

        $allowedValidator = $this->mockAccessValidator('allowed');
        $allowedValidator->shouldReceive('validateAccess')->withArgs($expectArrayAnnotation(['resource' => 'foo', 'privilege' => 'bar']))->once();
        $requirementsChecker->addAccessValidator($allowedValidator);

        Assert::noError(function () use ($requirementsChecker): void {
            $requirementsChecker->protectElement(new \ReflectionClass(TestAnnotationsPresenter::class));
        });
    }

    public function testProtectElementWithCaseMismatch(): void
    {
        $expectArrayAnnotation = fn (array $expected): \Closure => fn ($annotation): bool => $this->matchArray($expected, $annotation);

        $requirementsChecker = new SecurityAnnotations\RequirementsChecker();

        $roleValidator = $this->mockAccessValidator('ROLE');
        $roleValidator->shouldReceive('validateAccess')->withArgs($expectArrayAnnotation(['a', 'b', 'c']))->once();
        $roleValidator->shouldReceive('validateAccess')->withArgs(['d'])->once();
        $requirementsChecker->addAccessValidator($roleValidator);

        Assert::error(
            function () use ($requirementsChecker): void {
                $requirementsChecker->protectElement(new \ReflectionClass(TestAnnotationsPresenter::class));
            },
            E_USER_WARNING,
            'Case mismatch in security annotation name "role", correct name is "ROLE".',
        );
    }

    /**
     * @param string[] $expected
     * @param mixed $actual
     * @return bool
     */
    private function matchArray(array $expected, $actual): bool
    {
        if ($actual instanceof \Traversable) {
            $actual = iterator_to_array($actual);
        }

        return $expected === $actual;
    }

    /**
     * @param string $annotationName
     * @return AccessValidator|MockInterface
     */
    private function mockAccessValidator(string $annotationName): AccessValidator
    {
        $mock = \Mockery::mock(AccessValidator::class);
        $mock->shouldReceive('getSupportedAnnotationName')->andReturn($annotationName);

        return $mock;
    }

}


(new RequirementsCheckerTest())->run();
