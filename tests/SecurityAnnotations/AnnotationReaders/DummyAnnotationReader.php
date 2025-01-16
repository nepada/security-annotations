<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AnnotationReaders;

use Nepada\SecurityAnnotations\AnnotationReaders\AnnotationsReader;

final class DummyAnnotationReader implements AnnotationsReader
{

    /**
     * @var list<object>
     */
    private array $annotations;

    /**
     * @param list<object> $annotations
     */
    public function __construct(array $annotations)
    {
        $this->annotations = $annotations;
    }

    /**
     * @return list<object>
     */
    public function getAll(\Reflector $element): array
    {
        return $this->annotations;
    }

}
