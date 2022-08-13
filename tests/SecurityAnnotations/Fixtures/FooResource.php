<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\Fixtures;

use Nette\Security\Resource;

final class FooResource implements Resource
{

    public function getResourceId(): string
    {
        return 'foo';
    }

}
