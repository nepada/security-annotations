<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Nette;

trait SecurityAnnotations
{

    private RequirementsChecker $requirementsChecker;

    public function injectRequirementsChecker(RequirementsChecker $requirementsChecker): void
    {
        $this->requirementsChecker = $requirementsChecker;
    }

    /**
     * @param mixed $element
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function checkRequirements(mixed $element): void
    {
        parent::checkRequirements($element);

        if ($element instanceof \Reflector) {
            $this->requirementsChecker->protectElement($element);
        }
    }

}
