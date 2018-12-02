<?php
declare(strict_types = 1);

namespace Nepada\Bridges\SecurityAnnotationsDI;

use Nepada\SecurityAnnotations\AccessValidators\IAccessValidator;
use Nepada\SecurityAnnotations\AccessValidators\LoggedInValidator;
use Nepada\SecurityAnnotations\AccessValidators\PermissionValidator;
use Nepada\SecurityAnnotations\AccessValidators\RoleValidator;
use Nepada\SecurityAnnotations\AccessValidators\SameSiteValidator;
use Nepada\SecurityAnnotations\RequirementsChecker;
use Nette;
use Nette\Utils\Strings;

class SecurityAnnotationsExtension extends Nette\DI\CompilerExtension
{

    /** @var mixed[] */
    public $defaults = [
        'validators' => [
            'loggedIn' => LoggedInValidator::class,
            'role' => RoleValidator::class,
            'allowed' => PermissionValidator::class,
            'sameSite' => SameSiteValidator::class,
        ],
    ];

    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $requirementsChecker = $container->addDefinition($this->prefix('requirementsChecker'))
            ->setType(RequirementsChecker::class);

        foreach ($config['validators'] as $annotation => $validator) {
            if ($validator === false) {
                continue;
            }

            $validatorService = $this->getValidatorService($validator, $annotation);
            $requirementsChecker->addSetup('addAccessValidator', [$annotation, $validatorService]);
        }
    }

    private function getValidatorService(string $validator, string $annotation): string
    {
        if (Strings::startsWith($validator, '@')) {
            return $validator;
        }

        if (!class_exists($validator)) {
            throw new \LogicException("Access validator class '$validator' not found.");
        } elseif (!in_array(IAccessValidator::class, class_implements($validator), true)) {
            throw new \LogicException("Access validator class '$validator' must implement IAccessValidator interface.");
        }

        $serviceName = $this->prefix("accessValidator.$annotation");
        $this->getContainerBuilder()->addDefinition($serviceName)
            ->setType($validator);

        return "@{$serviceName}";
    }

}
