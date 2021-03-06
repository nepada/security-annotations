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
            new SecurityAnnotations\AnnotationReaders\DoctrineAnnotationsReader(new AnnotationReader(new DocParser())),
            new SecurityAnnotations\AnnotationReaders\DoctrineAnnotationsReader(new AnnotationReader(new DocParser())),
        );

        $expected = [
            new SecurityAnnotations\Annotations\LoggedIn(),
            new SecurityAnnotations\Annotations\Role(['a', 'b', 'c']),
            new SecurityAnnotations\Annotations\Role('d'),
            new SecurityAnnotations\Annotations\Allowed('foo', 'bar'),

            new SecurityAnnotations\Annotations\LoggedIn(),
            new SecurityAnnotations\Annotations\Role(['a', 'b', 'c']),
            new SecurityAnnotations\Annotations\Role('d'),
            new SecurityAnnotations\Annotations\Allowed('foo', 'bar'),
        ];

        $actual = $reader->getAll(new \ReflectionClass(TestAnnotationsPresenter::class));
        Assert::equal($expected, $actual);
    }

}


(new UnionReaderTest())->run();
