<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AnnotationReaders;

interface AnnotationsReader
{

    /**
     * @param \Reflector $element
     * @return object[]
     */
    public function getAll(\Reflector $element): array;

}
