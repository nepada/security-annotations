<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Nepada\SecurityAnnotations;
use Nette;


class SecuredComponentsPresenter extends Nette\Application\UI\Presenter
{

    use SecurityAnnotations\TSecuredComponents;
    use SecurityAnnotations\TSecurityAnnotations;


    protected function createComponentFoo(): SecuredComponentsControl
    {
        return new SecuredComponentsControl();
    }

}
