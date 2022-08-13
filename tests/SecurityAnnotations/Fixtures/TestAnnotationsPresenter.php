<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\Fixtures;

use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nepada\SecurityAnnotations\Annotations\LoggedIn;
use Nepada\SecurityAnnotations\Annotations\Role;
use Nette;

#[LoggedIn()]
#[Role('lorem')]
#[Role('foo', 'bar')]
#[Allowed('foo', 'bar')]
#[Allowed(privilege: 'shiny')]
class TestAnnotationsPresenter extends Nette\Application\UI\Presenter
{

}
