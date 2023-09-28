<?php
declare(strict_types = 1);

namespace Nepada\Bridges\SecurityAnnotationsDI;

use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;
use Nepada\SecurityAnnotations\AccessValidators\LoggedInValidator;
use Nepada\SecurityAnnotations\AccessValidators\PermissionValidator;
use Nepada\SecurityAnnotations\AccessValidators\RoleValidator;
use Nepada\SecurityAnnotations\AnnotationReaders\AnnotationsReader;
use Nepada\SecurityAnnotations\AnnotationReaders\AttributesReader;
use Nepada\SecurityAnnotations\AnnotationReaders\UnionReader;
use Nepada\SecurityAnnotations\RequirementsChecker;
use Nette;
use Nette\DI\Definitions\ServiceDefinition;
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

        $readers = [];
        $readers[] = $container->addDefinition($this->prefix('attributesReader'), new ServiceDefinition())
            ->setType(AttributesReader::class)
            ->setAutowired(AttributesReader::class);

        $container->addDefinition($this->prefix('annotationsReader'), new ServiceDefinition())
            ->setType(AnnotationsReader::class)
            ->setFactory(UnionReader::class, $readers);

        $requirementsChecker = $container->addDefinition($this->prefix('requirementsChecker'), new ServiceDefinition())
            ->setType(RequirementsChecker::class);

        $validators = $this->config->validators;
        if ($this->config->enableDefaultValidators) {
            $validators = array_merge(self::DEFAULT_VALIDATORS, $validators);
        }

        $validatorServices = [];
        foreach ($validators as $validator) {
            $validatorServices[] = $this->getValidatorService($validator);
        }

        $arguments = [null, ...$validatorServices];
        unset($arguments[0]);
        $requirementsChecker->setArguments($arguments);
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
        $this->getContainerBuilder()->addDefinition($serviceName, new ServiceDefinition())
            ->setType($validator);

        return "@{$serviceName}";
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
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
