<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AnnotationReaders;

use Nette;

final class AttributesReader implements AnnotationsReader
{

    use Nette\SmartObject;

    /**
     * @param \Reflector $element
     * @return object[]
     */
    public function getAll(\Reflector $element): array
    {
        if (PHP_VERSION_ID < 8_00_00) {
            return [];
        }

        if ($element instanceof \ReflectionMethod || $element instanceof \ReflectionClass) {
            return array_map(fn (\ReflectionAttribute $attribute): object => $attribute->newInstance(), $element->getAttributes());
        }

        return [];
    }

}
