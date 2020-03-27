<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\Fixtures;

use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nepada\SecurityAnnotations\Annotations\LoggedIn;
use Nepada\SecurityAnnotations\Annotations\Role;
use Nette;

/**
 * @LoggedIn
 * @Role({"a", "b", "c"})
 * @Role("d")
 * @Allowed(resource="foo", privilege="bar")
 * @author Foo Bar
 */
class TestAnnotationsPresenter extends Nette\Application\UI\Presenter
{

}
