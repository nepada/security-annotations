<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Nepada\SecurityAnnotations\AccessValidators\IAccessValidator;
use Nette;
use Nette\Reflection\AnnotationsParser;
use Nette\Utils\Strings;

class RequirementsChecker
{

    use Nette\SmartObject;

    /** @var IAccessValidator[] */
    private $accessValidators = [];

    /** @var string[] */
    private $annotationNames = [];

    public function addAccessValidator(string $annotationName, IAccessValidator $accessValidator): void
    {
        if (isset($this->accessValidators[$annotationName])) {
            throw new \LogicException("Access validator for annotation \"$annotationName\" is already registered.");
        }

        $lowerCaseAnnotationName = Strings::lower($annotationName);
        if (isset($this->annotationNames[$lowerCaseAnnotationName])) {
            $errorMessage = sprintf(
                'Access validator for annotation "%s" is case insensitive match for already registered access validator "%s".',
                $annotationName,
                $this->annotationNames[$lowerCaseAnnotationName]
            );
            throw new \LogicException($errorMessage);
        }

        $this->annotationNames[$lowerCaseAnnotationName] = $annotationName;
        $this->accessValidators[$annotationName] = $accessValidator;
    }

    /**
     * @param \Reflector $element
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function protectElement(\Reflector $element): void
    {
        foreach (AnnotationsParser::getAll($element) as $annotationName => $annotations) {
            if (! isset($this->accessValidators[$annotationName])) {
                $lowerCaseAnnotationName = Strings::lower($annotationName);
                if (! isset($this->annotationNames[$lowerCaseAnnotationName])) {
                    continue;
                }

                $errorMessage = sprintf(
                    'Case mismatch in security annotation name "%s", correct name is "%s".',
                    $annotationName,
                    $this->annotationNames[$lowerCaseAnnotationName]
                );
                trigger_error($errorMessage, E_USER_WARNING);
                $annotationName = $this->annotationNames[$lowerCaseAnnotationName];
            }

            $accessValidator = $this->accessValidators[$annotationName];
            foreach ($annotations as $annotation) {
                $accessValidator->validateAccess($annotation);
            }
        }
    }

}
