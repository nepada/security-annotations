<?php
/**
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\Bridges\SecurityAnnotationsDI;

use Nepada\SecurityAnnotations\AccessValidators\IAccessValidator;
use Nepada\SecurityAnnotations\AccessValidators\LoggedInValidator;
use Nepada\SecurityAnnotations\AccessValidators\PermissionValidator;
use Nepada\SecurityAnnotations\AccessValidators\RoleValidator;
use Nepada\SecurityAnnotations\InvalidStateException;
use Nepada\SecurityAnnotations\RequirementsChecker;
use Nette;
use Nette\Utils\Strings;


class SecurityAnnotationsExtension extends Nette\DI\CompilerExtension
{

    /** @var mixed[] */
    public $defaults = [
        'validators' => [
            'LoggedIn' => LoggedInValidator::class,
            'Role' => RoleValidator::class,
            'Allowed' => PermissionValidator::class,
        ],
    ];


    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $requirementsChecker = $container->addDefinition($this->prefix('requirementsChecker'))
            ->setClass(RequirementsChecker::class);

        foreach ($config['validators'] as $annotation => $validator) {
            if ($validator === false) {
                continue;
            }

            $validatorService = $this->getValidatorService($validator, $annotation);
            $requirementsChecker->addSetup('addAccessValidator', [$annotation, $validatorService]);
        }
    }

    /**
     * @param string $validator
     * @param string $annotation
     * @return string
     */
    private function getValidatorService(string $validator, string $annotation): string
    {
        if (Strings::startsWith($validator, '@')) {
            return $validator;
        }

        if (!class_exists($validator)) {
            throw new InvalidStateException("Access validator class '$validator' not found.");
        } elseif (!in_array(IAccessValidator::class, class_implements($validator), true)) {
            throw new InvalidStateException("Access validator class '$validator' must implement IAccessValidator interface.");
        }

        $serviceName = $this->prefix("accessValidator.$annotation");
        $this->getContainerBuilder()->addDefinition($serviceName)
            ->setClass($validator);

        return "@{$serviceName}";
    }

}
