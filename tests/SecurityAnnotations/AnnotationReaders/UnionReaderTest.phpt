<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AnnotationReaders;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
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
            new SecurityAnnotations\AnnotationReaders\DoctrineAnnotationsReader(new AnnotationReader(new DocParser())),
        );

        $expected = [
            new SecurityAnnotations\Annotations\LoggedIn(),
            new SecurityAnnotations\Annotations\Role(['a', 'b', 'c']),
            new SecurityAnnotations\Annotations\Role('d'),
            new SecurityAnnotations\Annotations\Allowed('foo', 'bar'),
        ];

        if (PHP_VERSION_ID >= 8_00_00) {
            $expected = array_merge(
                [
                    new SecurityAnnotations\Annotations\LoggedIn(),
                    new SecurityAnnotations\Annotations\Role('lorem'),
                    new SecurityAnnotations\Annotations\Role(['foo', 'bar']),
                    new SecurityAnnotations\Annotations\Allowed(null, 'shiny'),

                ],
                $expected,
            );
        }

        $actual = $reader->getAll(new \ReflectionClass(TestAnnotationsPresenter::class));
        Assert::equal($expected, $actual);
    }

}


(new UnionReaderTest())->run();
