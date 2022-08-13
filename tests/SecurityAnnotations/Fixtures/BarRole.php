<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\Fixtures;

use Nette\Security\Role;

final class BarRole implements Role
{

    public function getRoleId(): string
    {
        return 'role';
    }

}
