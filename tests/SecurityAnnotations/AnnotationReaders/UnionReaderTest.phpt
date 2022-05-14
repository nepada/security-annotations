<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AnnotationReaders;

use Nepada\SecurityAnnotations;
use NepadaTests\SecurityAnnotations\Fixtures\TestAnnotationsPresenter;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class UnionReaderTest extends TestCase
{

    public function testGetAll(): void
    {
        $reader = new SecurityAnnotations\AnnotationReaders\UnionReader(
            new SecurityAnnotations\AnnotationReaders\AttributesReader(),
            new SecurityAnnotations\AnnotationReaders\AttributesReader(),
        );

        $expected = [
            new SecurityAnnotations\Annotations\LoggedIn(),
            new SecurityAnnotations\Annotations\Role('lorem'),
            new SecurityAnnotations\Annotations\Role(['foo', 'bar']),
            new SecurityAnnotations\Annotations\Allowed('foo', 'bar'),
            new SecurityAnnotations\Annotations\Allowed(null, 'shiny'),

            new SecurityAnnotations\Annotations\LoggedIn(),
            new SecurityAnnotations\Annotations\Role('lorem'),
            new SecurityAnnotations\Annotations\Role(['foo', 'bar']),
            new SecurityAnnotations\Annotations\Allowed('foo', 'bar'),
            new SecurityAnnotations\Annotations\Allowed(null, 'shiny'),
        ];

        $actual = $reader->getAll(new \ReflectionClass(TestAnnotationsPresenter::class));
        Assert::equal($expected, $actual);
    }

}


(new UnionReaderTest())->run();
