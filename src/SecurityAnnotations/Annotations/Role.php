<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Attribute;
use Doctrine\Common\Annotations\NamedArgumentConstructorAnnotation;
use Nette;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Role implements NamedArgumentConstructorAnnotation
{

    use Nette\SmartObject;

    /**
     * @Required
     * @internal use getter instead
     * @var array<string>
     */
    public array $roles;

    /**
     * @param string|array<string> $value
     */
    public function __construct(string|array $value)
    {
        $this->roles = is_string($value) ? [$value] : $value;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

}
