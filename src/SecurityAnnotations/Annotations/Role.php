<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Doctrine\Common\Annotations\NamedArgumentConstructorAnnotation;
use Nette;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
final class Role implements NamedArgumentConstructorAnnotation
{

    use Nette\SmartObject;

    /**
     * @Required
     * @var array<string>
     */
    public array $roles;

    /**
     * @param string|array<string> $value
     */
    public function __construct($value)
    {
        $this->roles = is_string($value) ? [$value] : $value;
    }

}
