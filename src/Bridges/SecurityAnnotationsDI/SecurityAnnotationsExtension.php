<?php
declare(strict_types = 1);

namespace Nepada\Bridges\SecurityAnnotationsDI;

use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;
use Nepada\SecurityAnnotations\AccessValidators\LoggedInValidator;
use Nepada\SecurityAnnotations\AccessValidators\PermissionValidator;
use Nepada\SecurityAnnotations\AccessValidators\RoleValidator;
use Nepada\SecurityAnnotations\RequirementsChecker;
use Nette;
use Nette\Schema\Expect;
use Nette\Utils\Strings;

class SecurityAnnotationsExtension extends Nette\DI\CompilerExtension
{

    private const DEFAULT_VALIDATORS = [
        'loggedIn' => LoggedInValidator::class,
        'role' => RoleValidator::class,
        'allowed' => PermissionValidator::class,
    ];

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Expect::structure([
            'validators' => Expect::arrayOf(Expect::anyOf(Expect::string(), false))->default(self::DEFAULT_VALIDATORS),
        ]);
    }

    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();

        $requirementsChecker = $container->addDefinition($this->prefix('requirementsChecker'))
            ->setType(RequirementsChecker::class);

        foreach ($this->config->validators as $annotation => $validator) {
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

        if (! class_exists($validator)) {
            throw new \LogicException("Access validator class '$validator' not found.");
        } elseif (! in_array(AccessValidator::class, class_implements($validator), true)) {
            throw new \LogicException("Access validator class '$validator' must implement AccessValidator interface.");
        }

        $serviceName = $this->prefix("accessValidator.$annotation");
        $this->getContainerBuilder()->addDefinition($serviceName)
            ->setType($validator);

        return "@{$serviceName}";
    }

}
