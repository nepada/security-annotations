<?php
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
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param mixed $element
     * @return void
     */
    public function checkRequirements($element)
    {
        parent::checkRequirements($element);

        if ($element instanceof \Reflector) {
            $this->getRequirementsChecker()->protectElement($element);
        }
    }

}
