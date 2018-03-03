<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Mockery;
use Nepada\SecurityAnnotations;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class TSecuredComponentsTest extends TestCase
{

    public function testSecuredComponents(): void
    {
        $requirementsChecker = Mockery::mock(SecurityAnnotations\RequirementsChecker::class);
        $requirementsChecker->shouldReceive('protectElement')->once()->withArgs(function (\ReflectionMethod $element): bool {
            return $element->getName() === 'createComponentFoo' && $element->getDeclaringClass()->getName() === SecuredComponentsPresenter::class;
        });
        $requirementsChecker->shouldReceive('protectElement')->once()->withArgs(function (\ReflectionMethod $element): bool {
            return $element->getName() === 'createComponentFoo' && $element->getDeclaringClass()->getName() === SecuredComponentsControl::class;
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


(new TSecuredComponentsTest())->run();
