<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations;

use Nette;

/**
 * @LoggedIn
 * @Role(a, b, c)
 * @Role(d)
 * @Allowed(resource=foo, privilege=bar)
 * @Foo
 */
class TestAnnotationsPresenter extends Nette\Application\UI\Presenter
{

}
