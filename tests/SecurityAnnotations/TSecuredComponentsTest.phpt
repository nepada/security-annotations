<?php
/**
 * Test: Nepada\SecurityAnnotations\TSecuredComponents.
 *
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Mockery;
use Nepada;
use Nepada\SecurityAnnotations;
use NepadaTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';


class TSecuredComponentsTest extends TestCase
{

    public function testSecuredComponents(): void
    {
        $requirementsChecker = Mockery::mock(SecurityAnnotations\RequirementsChecker::class);
        $requirementsChecker->shouldReceive('protectElement')->once()->withArgs(function (\ReflectionMethod $element): bool {
            return $element->getName() === 'createComponentFoo' && $element->getDeclaringClass()->getName() == SecuredComponentsPresenter::class;
        });
        $requirementsChecker->shouldReceive('protectElement')->once()->withArgs(function (\ReflectionMethod $element): bool {
            return $element->getName() === 'createComponentFoo' && $element->getDeclaringClass()->getName() == SecuredComponentsControl::class;
        });

        Assert::noError(function () use ($requirementsChecker): void {
            $presenter = new SecuredComponentsPresenter();

            $presenter->setRequirementsChecker($requirementsChecker);
            $control = $presenter->getComponent('foo');

            $control->setRequirementsChecker($requirementsChecker);
            $control->getComponent('foo');
        });
    }

}


\run(new TSecuredComponentsTest());
