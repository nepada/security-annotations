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
class AttributesReaderTest extends TestCase
{

    public function testGetAll(): void
    {
        $reader = new SecurityAnnotations\AnnotationReaders\AttributesReader();

        $expected = [
            new SecurityAnnotations\Annotations\LoggedIn(),
            new SecurityAnnotations\Annotations\Role('lorem'),
            new SecurityAnnotations\Annotations\Role('foo', 'bar'),
            new SecurityAnnotations\Annotations\Allowed('foo', 'bar'),
            new SecurityAnnotations\Annotations\Allowed(null, 'shiny'),
        ];
        $actual = $reader->getAll(new \ReflectionClass(TestAnnotationsPresenter::class));
        Assert::equal($expected, $actual);
    }

    public function testDeprecatedSyntax(): void
    {
        $reader = new SecurityAnnotations\AnnotationReaders\AttributesReader();

        $expected = [
            new SecurityAnnotations\Annotations\Role('foo', 'bar'),
        ];
        Assert::error(
            function () use ($reader, $expected): void {
                $actual = $reader->getAll(new \ReflectionMethod(TestAnnotationsPresenter::class, 'deprecated'));
                Assert::equal($expected, $actual);
            },
            E_USER_DEPRECATED,
            'Passing roles as a single array argument is deprecated, use variadic argument instead',
        );
    }

}


(new AttributesReaderTest())->run();
