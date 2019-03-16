<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\Fixtures;

use Nepada\SecurityAnnotations;
use Nette;

class SecuredControl extends Nette\Application\UI\Control
{

    use SecurityAnnotations\TSecurityAnnotations;

}
