<?php
/**
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Nette;


trait TSecurityAnnotations
{

    /** @var RequirementsChecker|null */
    private $requirementsChecker;


    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param bool $throw
     * @return Nette\Application\UI\Presenter|null
     */
    abstract public function getPresenter($throw = true);

    /**
     * @return RequirementsChecker
     */
    public function getRequirementsChecker(): RequirementsChecker
    {
        if ($this->requirementsChecker !== null) {
            return $this->requirementsChecker;
        }

        $presenter = $this->getPresenter();
        \assert($presenter instanceof Nette\Application\UI\Presenter); // workaround for PHPStan false positive detection of possible null value

        return $presenter->getContext()->getByType(RequirementsChecker::class);
    }

    /**
     * @param RequirementsChecker $requirementsChecker
     */
    public function setRequirementsChecker(RequirementsChecker $requirementsChecker): void
    {
        $this->requirementsChecker = $requirementsChecker;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param mixed $element
     */
    public function checkRequirements($element)
    {
        parent::checkRequirements($element);

        if ($element instanceof \Reflector) {
            $this->getRequirementsChecker()->protectElement($element);
        }
    }

}
