<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\Fixtures;

use Nepada\SecurityAnnotations;
use Nette;

class SecuredComponentsControl extends Nette\Application\UI\Control
{

    use SecurityAnnotations\TSecuredComponents;
    use SecurityAnnotations\TSecurityAnnotations;

    protected function createComponentFoo(): SecuredComponentsControl
    {
        return new SecuredComponentsControl();
    }

}
