<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AnnotationReaders;

use Nette;

final class UnionReader implements AnnotationsReader
{

    use Nette\SmartObject;

    /**
     * @var list<AnnotationsReader>
     */
    private array $readers;

    public function __construct(AnnotationsReader ...$readers)
    {
        $this->readers = array_values($readers);
    }

    /**
     * @return list<object>
     */
    public function getAll(\Reflector $element): array
    {
        $annotations = [];
        foreach ($this->readers as $reader) {
            $annotations = array_merge($annotations, $reader->getAll($element));
        }
        return $annotations;
    }

}
