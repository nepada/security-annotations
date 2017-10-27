<?php
/**
 * Test: Nepada\SecurityAnnotations\RequirementsCheckerTest.
 *
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Mockery;
use Nepada;
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

        Assert::exception(function () use ($requirementsChecker): void {
            $requirementsChecker->addAccessValidator('duplicate', Mockery::mock(IAccessValidator::class));
        }, SecurityAnnotations\InvalidStateException::class, 'Access validator for annotation \'duplicate\' is already registered.');
    }

    public function testProtectElement(): void
    {
        $expectArrayAnnotation = function (array $expected): callable {
            return function ($annotation) use ($expected): bool {
                return $this->matchArray($expected, $annotation);
            };
        };

        $requirementsChecker = new SecurityAnnotations\RequirementsChecker();

        $loggedInValidator = Mockery::mock(IAccessValidator::class);
        $loggedInValidator->shouldReceive('validateAccess')->withArgs([true])->once();
        $requirementsChecker->addAccessValidator('LoggedIn', $loggedInValidator);

        $roleValidator = Mockery::mock(IAccessValidator::class);
        $roleValidator->shouldReceive('validateAccess')->withArgs($expectArrayAnnotation(['a', 'b', 'c']))->once();
        $roleValidator->shouldReceive('validateAccess')->withArgs(['d'])->once();
        $requirementsChecker->addAccessValidator('Role', $roleValidator);

        $allowedValidator = Mockery::mock(IAccessValidator::class);
        $allowedValidator->shouldReceive('validateAccess')->withArgs($expectArrayAnnotation(['resource' => 'foo', 'privilege' => 'bar']))->once();
        $requirementsChecker->addAccessValidator('Allowed', $allowedValidator);

        Assert::noError(function () use ($requirementsChecker): void {
            $requirementsChecker->protectElement(new \ReflectionClass(TestAnnotationsPresenter::class));
        });
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


\run(new RequirementsCheckerTest());
