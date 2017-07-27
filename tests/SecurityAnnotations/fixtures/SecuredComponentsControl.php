<?php
/**
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Nepada\SecurityAnnotations;
use Nette;


class SecuredComponentsControl extends Nette\Application\UI\Control
{

    use SecurityAnnotations\TSecuredComponents;
    use SecurityAnnotations\TSecurityAnnotations;


    /**
     * @return SecuredComponentsControl
     */
    protected function createComponentFoo(): SecuredComponentsControl
    {
        return new SecuredComponentsControl();
    }

}