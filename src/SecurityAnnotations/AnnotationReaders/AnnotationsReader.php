<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AnnotationReaders;

interface AnnotationsReader
{

    /**
     * @return object[]
     */
    public function getAll(\Reflector $element): array;

}
