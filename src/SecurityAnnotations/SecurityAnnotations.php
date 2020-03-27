<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Nette;

trait SecurityAnnotations
{

    private ?RequirementsChecker $requirementsChecker = null;

    abstract public function getPresenter(): ?Nette\Application\UI\Presenter;

    public function getRequirementsChecker(): RequirementsChecker
    {
        if ($this->requirementsChecker !== null) {
            return $this->requirementsChecker;
        }

        $presenter = $this->getPresenter();

        return $presenter->getContext()->getByType(RequirementsChecker::class);
    }

    public function setRequirementsChecker(RequirementsChecker $requirementsChecker): void
    {
        $this->requirementsChecker = $requirementsChecker;
    }

    /**
     * @param mixed $element
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function checkRequirements($element): void
    {
        parent::checkRequirements($element);

        if ($element instanceof \Reflector) {
            $this->getRequirementsChecker()->protectElement($element);
        }
    }

}
