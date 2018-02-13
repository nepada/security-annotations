<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations;

use Nette\ComponentModel\IComponent;


trait TSecuredComponents
{

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param mixed $element
     */
    abstract public function checkRequirements($element);

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param string $name
     * @return IComponent|null
     */
    protected function createComponent($name)
    {
        $method = 'createComponent' . ucfirst($name);
        if (method_exists($this, $method)) {
            $methodReflection = new \ReflectionMethod($this, $method);
            $this->checkRequirements($methodReflection);
        }

        return parent::createComponent($name);
    }

}
