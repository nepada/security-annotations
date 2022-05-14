<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Nette;
use Nette\ComponentModel\IComponent;

trait SecuredComponents
{

    /**
     * @param mixed $element
     * @throws Nette\Application\ForbiddenRequestException;
     */
    abstract public function checkRequirements(mixed $element): void;

    protected function createComponent(string $name): ?IComponent
    {
        $method = 'createComponent' . ucfirst($name);
        if (method_exists($this, $method)) {
            $methodReflection = new \ReflectionMethod($this, $method);
            $this->checkRequirements($methodReflection);
        }

        return parent::createComponent($name);
    }

}
