<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\SecurityAnnotationsDI;

use Nepada\SecurityAnnotations;
use Nepada\SecurityAnnotations\AccessValidators\LoggedInValidator;
use Nepada\SecurityAnnotations\AccessValidators\PermissionValidator;
use Nepada\SecurityAnnotations\AccessValidators\RoleValidator;
use NepadaTests\Bridges\SecurityAnnotationsDI\Fixtures\BarValidator;
use NepadaTests\Bridges\SecurityAnnotationsDI\Fixtures\Foo\FooValidator as FooValidator2;
use NepadaTests\Bridges\SecurityAnnotationsDI\Fixtures\FooValidator;
use NepadaTests\SecurityAnnotations\Fixtures\SecuredPresenter;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class SecurityAnnotationsExtensionTest extends TestCase
{

    private Nette\Configurator $configurator;

    public function setUp(): void
    {
        $this->configurator = new Nette\Configurator();
        $this->configurator->setTempDirectory(TEMP_DIR);
        $this->configurator->setDebugMode(true);
        $this->configurator->addConfig(__DIR__ . '/Fixtures/config.neon');
    }

    public function testDefaultValidators(): void
    {
        $container = $this->configurator->createContainer();

        Assert::type(LoggedInValidator::class, $container->getService('securityAnnotations.loggedInValidator'));
        Assert::type(RoleValidator::class, $container->getService('securityAnnotations.roleValidator'));
        Assert::type(PermissionValidator::class, $container->getService('securityAnnotations.permissionValidator'));

        $requirementsChecker = $container->getService('securityAnnotations.requirementsChecker');
        Assert::type(SecurityAnnotations\RequirementsChecker::class, $requirementsChecker);

        $reflection = new \ReflectionProperty(SecurityAnnotations\RequirementsChecker::class, 'accessValidators');
        $reflection->setAccessible(true);
        $accessValidators = $reflection->getValue($requirementsChecker);
        Assert::type('array', $accessValidators);
        Assert::count(3, $accessValidators);
        Assert::type(LoggedInValidator::class, $accessValidators['loggedIn']);
        Assert::type(RoleValidator::class, $accessValidators['role']);
        Assert::type(PermissionValidator::class, $accessValidators['allowed']);
    }

    public function testCustomValidators(): void
    {
        $this->configurator->addConfig(__DIR__ . '/Fixtures/config.custom-validators.neon');
        $container = $this->configurator->createContainer();

        Assert::type(FooValidator::class, $container->getService('securityAnnotations.fooValidator'));
        Assert::type(FooValidator2::class, $container->getService('securityAnnotations.fooValidator_2'));

        $requirementsChecker = $container->getByType(SecurityAnnotations\RequirementsChecker::class);

        $reflection = new \ReflectionProperty(SecurityAnnotations\RequirementsChecker::class, 'accessValidators');
        $reflection->setAccessible(true);
        $accessValidators = $reflection->getValue($requirementsChecker);
        Assert::type('array', $accessValidators);
        Assert::count(3, $accessValidators);
        Assert::type(FooValidator::class, $accessValidators['foo']);
        Assert::type(FooValidator2::class, $accessValidators['foo2']);
        Assert::type(BarValidator::class, $accessValidators['bar']);
    }

    public function testInvalidValidator(): void
    {
        $this->configurator->addConfig(__DIR__ . '/Fixtures/config.invalid-validator.neon');

        Assert::exception(
            function (): void {
                $this->configurator->createContainer();
            },
            \LogicException::class,
            sprintf('Access validator class \'%s\' must implement AccessValidator interface.', SecuredPresenter::class),
        );
    }

    public function testNotFoundValidator(): void
    {
        $this->configurator->addConfig(__DIR__ . '/Fixtures/config.not-found-validator.neon');

        Assert::exception(
            function (): void {
                $this->configurator->createContainer();
            },
            \LogicException::class,
            'Access validator class \'NotFoundValidator\' not found.',
        );
    }

}


(new SecurityAnnotationsExtensionTest())->run();
