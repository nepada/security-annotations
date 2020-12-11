<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AnnotationReaders;

use Doctrine\Common\Annotations\Reader;
use Nette;

final class DoctrineAnnotationsReader implements AnnotationsReader
{

    use Nette\SmartObject;

    private Reader $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param \Reflector $element
     * @return object[]
     */
    public function getAll(\Reflector $element): array
    {
        if ($element instanceof \ReflectionMethod) {
            return $this->annotationReader->getMethodAnnotations($element);
        }

        if ($element instanceof \ReflectionClass) {
            return $this->annotationReader->getClassAnnotations($element);
        }

        if ($element instanceof \ReflectionProperty) {
            return $this->annotationReader->getPropertyAnnotations($element);
        }

        return [];
    }

}
