<?php
/**
 * This file is part of the nepada/security-annotations.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

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
