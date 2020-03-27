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

/**
 * @property \stdClass $config
 */
class SecurityAnnotationsExtension extends Nette\DI\CompilerExtension
{

    private const DEFAULT_VALIDATORS = [
        LoggedInValidator::class,
        RoleValidator::class,
        PermissionValidator::class,
    ];

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Expect::structure([
            'enableDefaultValidators' => Expect::bool(true),
            'validators' => Expect::listOf(Expect::string()),
        ]);
    }

    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();

        $requirementsChecker = $container->addDefinition($this->prefix('requirementsChecker'))
            ->setType(RequirementsChecker::class);

        $validators = $this->config->validators;
        if ($this->config->enableDefaultValidators) {
            $validators = array_merge(self::DEFAULT_VALIDATORS, $validators);
        }

        foreach ($validators as $validator) {
            $validatorService = $this->getValidatorService($validator);
            $requirementsChecker->addSetup('addAccessValidator', [$validatorService]);
        }
    }

    private function getValidatorService(string $validator): string
    {
        if (Strings::startsWith($validator, '@')) {
            return $validator;
        }

        if (! class_exists($validator)) {
            throw new \LogicException("Access validator class '$validator' not found.");
        }

        $reflection = new \ReflectionClass($validator);
        if (! $reflection->implementsInterface(AccessValidator::class)) {
            throw new \LogicException("Access validator class '$validator' must implement AccessValidator interface.");
        }

        $serviceName = $this->generateValidatorServiceName($reflection);
        $this->getContainerBuilder()->addDefinition($serviceName)
            ->setType($validator);

        return "@{$serviceName}";
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     * @return string
     */
    private function generateValidatorServiceName(\ReflectionClass $reflectionClass): string
    {
        $container = $this->getContainerBuilder();

        $shortName = Strings::firstLower($reflectionClass->getShortName());
        $serviceName = $this->prefix($shortName);
        $i = 1;
        while ($container->hasDefinition($serviceName)) {
            $i++;
            $serviceName = $this->prefix("{$shortName}_{$i}");
        }
        return $serviceName;
    }

}
