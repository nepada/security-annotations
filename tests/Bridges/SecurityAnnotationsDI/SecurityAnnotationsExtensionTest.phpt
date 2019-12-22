<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\SecurityAnnotationsDI;

use Nepada\SecurityAnnotations;
use NepadaTests\Bridges\SecurityAnnotationsDI\Fixtures\BarValidator;
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

        Assert::type(SecurityAnnotations\AccessValidators\LoggedInValidator::class, $container->getService('securityAnnotations.accessValidator.loggedIn'));
        Assert::type(SecurityAnnotations\AccessValidators\RoleValidator::class, $container->getService('securityAnnotations.accessValidator.role'));
        Assert::type(SecurityAnnotations\AccessValidators\PermissionValidator::class, $container->getService('securityAnnotations.accessValidator.allowed'));
        Assert::type(SecurityAnnotations\AccessValidators\SameSiteValidator::class, $container->getService('securityAnnotations.accessValidator.sameSite'));

        $requirementsChecker = $container->getService('securityAnnotations.requirementsChecker');
        Assert::type(SecurityAnnotations\RequirementsChecker::class, $requirementsChecker);

        $reflection = new \ReflectionProperty(SecurityAnnotations\RequirementsChecker::class, 'accessValidators');
        $reflection->setAccessible(true);
        $accessValidators = $reflection->getValue($requirementsChecker);
        Assert::type('array', $accessValidators);
        Assert::count(4, $accessValidators);
        Assert::type(SecurityAnnotations\AccessValidators\LoggedInValidator::class, $accessValidators['loggedIn']);
        Assert::type(SecurityAnnotations\AccessValidators\RoleValidator::class, $accessValidators['role']);
        Assert::type(SecurityAnnotations\AccessValidators\PermissionValidator::class, $accessValidators['allowed']);
        Assert::type(SecurityAnnotations\AccessValidators\SameSiteValidator::class, $accessValidators['sameSite']);
    }

    public function testCustomValidators(): void
    {
        $this->configurator->addConfig(__DIR__ . '/Fixtures/config.custom-validators.neon');
        $container = $this->configurator->createContainer();
        $requirementsChecker = $container->getByType(SecurityAnnotations\RequirementsChecker::class);

        $reflection = new \ReflectionProperty(SecurityAnnotations\RequirementsChecker::class, 'accessValidators');
        $reflection->setAccessible(true);
        $accessValidators = $reflection->getValue($requirementsChecker);
        Assert::type('array', $accessValidators);
        Assert::count(2, $accessValidators);
        Assert::type(FooValidator::class, $accessValidators['foo']);
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
            sprintf('Access validator class \'%s\' must implement IAccessValidator interface.', SecuredPresenter::class)
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
            'Access validator class \'NotFoundValidator\' not found.'
        );
    }

}


(new SecurityAnnotationsExtensionTest())->run();
