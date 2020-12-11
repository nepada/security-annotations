<?php
declare(strict_types = 1);

namespace Nepada\SecurityAnnotations\Annotations;

use Attribute;
use Nette;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class LoggedIn
{

    use Nette\SmartObject;

}
