<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AnnotationReaders;

use Nepada\SecurityAnnotations\AnnotationReaders\AnnotationsReader;

final class DummyAnnotationReader implements AnnotationsReader
{

    /**
     * @var object[]
     */
    private array $annotations;

    /**
     * @param object[] $annotations
     */
    public function __construct(array $annotations)
    {
        $this->annotations = $annotations;
    }

    /**
     * @return object[]
     */
    public function getAll(\Reflector $element): array
    {
        return $this->annotations;
    }

}
