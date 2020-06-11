<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Doctrine\Common\Annotations\Reader;
use Nepada\SecurityAnnotations\AccessValidators\AccessValidator;
use Nette;

class RequirementsChecker
{

    use Nette\SmartObject;

    private Reader $annotationReader;

    /**
     * @var array<class-string, AccessValidator>
     */
    private array $accessValidators = [];

    public function __construct(Reader $annotationReader, AccessValidator ...$accessValidators)
    {
        $this->annotationReader = $annotationReader;
        foreach ($accessValidators as $accessValidator) {
            $this->addAccessValidator($accessValidator);
        }
    }

    /**
     * @param \Reflector $element
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function protectElement(\Reflector $element): void
    {
        $annotations = $this->readAnnotations($element);
        foreach ($annotations as $annotation) {
            $annotationName = get_class($annotation);
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

    /**
     * @param \Reflector $element
     * @return object[]
     */
    private function readAnnotations(\Reflector $element): array
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
