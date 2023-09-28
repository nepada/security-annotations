<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AnnotationReaders;

use Nette;

final class UnionReader implements AnnotationsReader
{

    use Nette\SmartObject;

    /**
     * @var AnnotationsReader[]
     */
    private array $readers;

    public function __construct(AnnotationsReader ...$readers)
    {
        $this->readers = $readers;
    }

    /**
     * @return object[]
     */
    public function getAll(\Reflector $element): array
    {
        $annotations = [];
        foreach ($this->readers as $reader) {
            $annotations = array_merge($annotations, array_values($reader->getAll($element)));
        }
        return $annotations;
    }

}
