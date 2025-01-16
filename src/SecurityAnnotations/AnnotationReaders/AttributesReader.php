<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AnnotationReaders;

use Nette;

final class AttributesReader implements AnnotationsReader
{

    use Nette\SmartObject;

    /**
     * @return list<object>
     */
    public function getAll(\Reflector $element): array
    {
        if ($element instanceof \ReflectionMethod || $element instanceof \ReflectionClass) {
            return array_map(fn (\ReflectionAttribute $attribute): object => $attribute->newInstance(), $element->getAttributes());
        }

        return [];
    }

}
