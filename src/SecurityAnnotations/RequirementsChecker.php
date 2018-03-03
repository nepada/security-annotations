<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Nepada\SecurityAnnotations\AccessValidators\IAccessValidator;
use Nette;
use Nette\Reflection\AnnotationsParser;
use Nette\Utils\Arrays;

class RequirementsChecker
{

    use Nette\SmartObject;

    /** @var IAccessValidator[] */
    private $accessValidators = [];

    /**
     * @param string $annotation
     * @param IAccessValidator $accessValidator
     * @throws InvalidStateException
     */
    public function addAccessValidator(string $annotation, IAccessValidator $accessValidator): void
    {
        if (isset($this->accessValidators[$annotation])) {
            throw new InvalidStateException("Access validator for annotation '$annotation' is already registered.");
        }

        $this->accessValidators[$annotation] = $accessValidator;
    }

    /**
     * @param \Reflector $element
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function protectElement(\Reflector $element): void
    {
        $annotations = AnnotationsParser::getAll($element);

        foreach ($this->accessValidators as $annotation => $accessValidator) {
            foreach (Arrays::get($annotations, $annotation, []) as $annotationValue) {
                $accessValidator->validateAccess($annotationValue);
            }
        }
    }

}
