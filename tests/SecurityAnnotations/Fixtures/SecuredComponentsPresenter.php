<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\Fixtures;

use Nepada\SecurityAnnotations;
use Nette;

class SecuredComponentsPresenter extends Nette\Application\UI\Presenter
{

    use SecurityAnnotations\SecuredComponents;
    use SecurityAnnotations\SecurityAnnotations;

    protected function createComponentFoo(): SecuredComponentsControl
    {
        $control = new SecuredComponentsControl();
        $control->injectRequirementsChecker($this->requirementsChecker);
        return $control;
    }

}
