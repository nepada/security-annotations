<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\AccessValidators;

use Nette;

class SameSiteValidator implements IAccessValidator
{

    use Nette\SmartObject;

    private Nette\Http\Request $request;

    public function __construct(Nette\Http\Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param mixed $annotation parsed value of annotation
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function validateAccess($annotation): void
    {
        if (! is_bool($annotation)) {
            throw new \InvalidArgumentException('Unexpected annotation type, bool expected.');
        }

        if ($annotation && ! $this->request->isSameSite()) {
            throw new Nette\Application\ForbiddenRequestException('Cross-site HTTP request not allowed.');
        }
    }

}
