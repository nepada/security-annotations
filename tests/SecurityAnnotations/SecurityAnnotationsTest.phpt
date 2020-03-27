<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Mockery;
use Nepada\SecurityAnnotations;
use NepadaTests\SecurityAnnotations\Fixtures\SecuredPresenter;
use NepadaTests\TestCase;
use Nette;
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

        $presenter = new SecuredPresenter();
        $presenter->injectPrimary(null, null, null, new Nette\Http\Request(new Nette\Http\UrlScript('http://example.com')), new Nette\Http\Response());
        $presenter->injectRequirementsChecker($requirementsChecker);
        Assert::noError(function () use ($presenter): void {
            $request = new Nette\Application\Request('SecuredPresenter', 'GET', ['action' => 'default']);
            $presenter->run($request);
        });
    }

}


(new SecurityAnnotationsTest())->run();
