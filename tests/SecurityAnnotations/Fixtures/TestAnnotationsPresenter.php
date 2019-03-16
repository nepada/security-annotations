<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\Fixtures;

use Nette;

/**
 * @loggedIn
 * @role(a, b, c)
 * @role(d)
 * @allowed(resource=foo, privilege=bar)
 * @foo
 */
class TestAnnotationsPresenter extends Nette\Application\UI\Presenter
{

}
