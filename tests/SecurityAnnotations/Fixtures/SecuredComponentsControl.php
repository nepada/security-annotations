<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\Fixtures;

use Nepada\SecurityAnnotations;
use Nette;

class SecuredComponentsControl extends Nette\Application\UI\Control
{

    use SecurityAnnotations\SecuredComponents;
    use SecurityAnnotations\SecurityAnnotations;

    protected function createComponentFoo(): self
    {
        return new self();
    }

}
