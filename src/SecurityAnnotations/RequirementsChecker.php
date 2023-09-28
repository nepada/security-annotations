<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;
use Nepada\SecurityAnnotations\AnnotationReaders\AnnotationsReader;
use Nette;

class RequirementsChecker
{

    use Nette\SmartObject;

    private AnnotationsReader $annotationReader;

    /**
     * @var array<class-string, AccessValidator>
     */
    private array $accessValidators = [];

    public function __construct(AnnotationsReader $annotationReader, AccessValidator ...$accessValidators)
    {
        $this->annotationReader = $annotationReader;
        foreach ($accessValidators as $accessValidator) {
            $this->addAccessValidator($accessValidator);
        }
    }

    /**
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function protectElement(\Reflector $element): void
    {
        $annotations = $this->annotationReader->getAll($element);
        foreach ($annotations as $annotation) {
            $annotationName = $annotation::class;
            if (isset($this->accessValidators[$annotationName])) {
                $this->accessValidators[$annotationName]->validateAccess($annotation);
            }
        }
    }

    private function addAccessValidator(AccessValidator $accessValidator): void
    {
        $annotationName = $accessValidator->getSupportedAnnotationName();
        if (! class_exists($annotationName)) {
            throw new \LogicException("Annotation class $annotationName does not exist.");
        }

        $reflection = new \ReflectionClass($annotationName);
        $normalizedName = $reflection->getName();

        if (isset($this->accessValidators[$normalizedName])) {
            throw new \LogicException("Access validator for annotation $normalizedName is already registered.");
        }

        $this->accessValidators[$annotationName] = $accessValidator;
    }

}
