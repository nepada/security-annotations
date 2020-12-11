<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Nette;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
final class Role
{

    use Nette\SmartObject;

    /**
     * @Required
     * @var array<string>
     */
    public array $roles;

}
