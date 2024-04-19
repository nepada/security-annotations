<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Nette;
use Nette\ComponentModel\IComponent;

/**
 * @deprecated Implemented directly in nette/application since version 3.2.2
 */
trait SecuredComponents
{

    /**
     * @throws Nette\Application\ForbiddenRequestException
     */
    abstract public function checkRequirements(\ReflectionMethod $element): void;

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
