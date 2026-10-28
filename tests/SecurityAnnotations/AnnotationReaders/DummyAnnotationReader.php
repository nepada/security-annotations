<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AnnotationReaders;

use Nepada\SecurityAnnotations\AnnotationReaders\AnnotationsReader;

final class DummyAnnotationReader implements AnnotationsReader
{

    /**
     * @param list<object> $annotations
     */
    public function __construct(
        private array $annotations,
    )
    {
    }

    /**
     * @return list<object>
     */
    public function getAll(\Reflector $element): array
    {
        return $this->annotations;
    }

}
