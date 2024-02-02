<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Mockery;
use Nepada\SecurityAnnotations;
use NepadaTests\SecurityAnnotations\Fixtures\SecuredPresenter;
use NepadaTests\TestCase;
use Nette;
use ReflectionMethod;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class SecurityAnnotationsTest extends TestCase
{

    public function testCheckRequirements(): void
    {
        $requirementsChecker = Mockery::mock(SecurityAnnotations\RequirementsChecker::class);
        $requirementsChecker->shouldReceive('protectElement')->times(3);

        $presenter = $this->createSecuredPresenter($requirementsChecker);
        Assert::noError(function () use ($presenter): void {
            $request = new Nette\Application\Request('SecuredPresenter', 'GET', ['action' => 'default']);
            $presenter->run($request);
        });
    }

    private function createSecuredPresenter(SecurityAnnotations\RequirementsChecker $requirementsChecker): SecuredPresenter
    {
        $presenter = new SecuredPresenter();

        $primaryDependencies = [];
        $rc = new ReflectionMethod($presenter, 'injectPrimary');
        foreach ($rc->getParameters() as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                continue;
            }
            $primaryDependencies[$parameter->getName()] = null;
        }
        $primaryDependencies['httpRequest'] = new Nette\Http\Request(new Nette\Http\UrlScript('http://example.com'));
        $primaryDependencies['httpResponse'] = new Nette\Http\Response();

        $presenter->injectPrimary(...$primaryDependencies);
        $presenter->injectRequirementsChecker($requirementsChecker);

        return $presenter;
    }

}


(new SecurityAnnotationsTest())->run();
