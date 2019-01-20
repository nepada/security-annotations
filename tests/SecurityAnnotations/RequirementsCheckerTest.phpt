<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Mockery;
use Nepada\SecurityAnnotations;
use Nepada\SecurityAnnotations\AccessValidators\IAccessValidator;
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
        $requirementsChecker->addAccessValidator('duplicate', Mockery::mock(IAccessValidator::class));

        Assert::exception(
            function () use ($requirementsChecker): void {
                $requirementsChecker->addAccessValidator('duplicate', Mockery::mock(IAccessValidator::class));
            },
            \LogicException::class,
            'Access validator for annotation "duplicate" is already registered.'
        );
    }

    public function testAddCaseInsensitiveDuplicateAccessValidator(): void
    {
        $requirementsChecker = new SecurityAnnotations\RequirementsChecker();
        $requirementsChecker->addAccessValidator('duplicate', Mockery::mock(IAccessValidator::class));

        Assert::exception(
            function () use ($requirementsChecker): void {
                $requirementsChecker->addAccessValidator('DUPLICATE', Mockery::mock(IAccessValidator::class));
            },
            \LogicException::class,
            'Access validator for annotation "DUPLICATE" is case insensitive match for already registered access validator "duplicate".'
        );
    }

    public function testProtectElement(): void
    {
        $expectArrayAnnotation = function (array $expected): \Closure {
            return function ($annotation) use ($expected): bool {
                return $this->matchArray($expected, $annotation);
            };
        };

        $requirementsChecker = new SecurityAnnotations\RequirementsChecker();

        $loggedInValidator = Mockery::mock(IAccessValidator::class);
        $loggedInValidator->shouldReceive('validateAccess')->withArgs([true])->once();
        $requirementsChecker->addAccessValidator('loggedIn', $loggedInValidator);

        $roleValidator = Mockery::mock(IAccessValidator::class);
        $roleValidator->shouldReceive('validateAccess')->withArgs($expectArrayAnnotation(['a', 'b', 'c']))->once();
        $roleValidator->shouldReceive('validateAccess')->withArgs(['d'])->once();
        $requirementsChecker->addAccessValidator('role', $roleValidator);

        $allowedValidator = Mockery::mock(IAccessValidator::class);
        $allowedValidator->shouldReceive('validateAccess')->withArgs($expectArrayAnnotation(['resource' => 'foo', 'privilege' => 'bar']))->once();
        $requirementsChecker->addAccessValidator('allowed', $allowedValidator);

        Assert::noError(function () use ($requirementsChecker): void {
            $requirementsChecker->protectElement(new \ReflectionClass(TestAnnotationsPresenter::class));
        });
    }

    public function testProtectElementWithCaseMismatch(): void
    {
        $expectArrayAnnotation = function (array $expected): \Closure {
            return function ($annotation) use ($expected): bool {
                return $this->matchArray($expected, $annotation);
            };
        };

        $requirementsChecker = new SecurityAnnotations\RequirementsChecker();

        $roleValidator = Mockery::mock(IAccessValidator::class);
        $roleValidator->shouldReceive('validateAccess')->withArgs($expectArrayAnnotation(['a', 'b', 'c']))->once();
        $roleValidator->shouldReceive('validateAccess')->withArgs(['d'])->once();
        $requirementsChecker->addAccessValidator('ROLE', $roleValidator);

        Assert::error(
            function () use ($requirementsChecker): void {
                $requirementsChecker->protectElement(new \ReflectionClass(TestAnnotationsPresenter::class));
            },
            E_USER_WARNING,
            'Case mismatch in security annotation name "role", correct name is "ROLE".'
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

}


(new RequirementsCheckerTest())->run();
