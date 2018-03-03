<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Nepada\SecurityAnnotations;
use Nette;


class SecuredPresenter extends Nette\Application\UI\Presenter
{

    use SecurityAnnotations\TSecurityAnnotations;

    /** @var bool */
    public $autoCanonicalize = false;


    public function actionDefault(): void
    {
    }

    public function renderDefault(): void
    {
        $this->sendResponse(new Nette\Application\Responses\TextResponse(''));
    }

    protected function createComponentFoo(): SecuredControl
    {
        return new SecuredControl();
    }

}
