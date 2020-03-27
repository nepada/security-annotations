<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Mockery;
use Nepada\SecurityAnnotations;
use NepadaTests\SecurityAnnotations\Fixtures\SecuredComponentsControl;
use NepadaTests\SecurityAnnotations\Fixtures\SecuredComponentsPresenter;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class SecuredComponentsTest extends TestCase
{

    public function testSecuredComponents(): void
    {
        $requirementsChecker = Mockery::mock(SecurityAnnotations\RequirementsChecker::class);
        $requirementsChecker->shouldReceive('protectElement')
            ->once()
            ->withArgs(fn (\ReflectionMethod $element): bool => $element->getName() === 'createComponentFoo' && $element->getDeclaringClass()->getName() === SecuredComponentsPresenter::class);
        $requirementsChecker->shouldReceive('protectElement')
            ->once()
            ->withArgs(fn (\ReflectionMethod $element): bool => $element->getName() === 'createComponentFoo' && $element->getDeclaringClass()->getName() === SecuredComponentsControl::class);

        Assert::noError(function () use ($requirementsChecker): void {
            $presenter = new SecuredComponentsPresenter();

            $presenter->setRequirementsChecker($requirementsChecker);
            $control = $presenter->getComponent('foo');

            $control->setRequirementsChecker($requirementsChecker);
            $control->getComponent('foo');
        });
    }

}


(new SecuredComponentsTest())->run();
