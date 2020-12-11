<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Nette;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
final class LoggedIn
{

    use Nette\SmartObject;

}
