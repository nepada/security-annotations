<?php
declare(strict_types = 1);

namespace NepadaTests\SecurityAnnotations\AccessValidators;

use Mockery;
use Mockery\MockInterface;
use Nepada\SecurityAnnotations\AccessValidators;
use NepadaTests\TestCase;
use Nette;
use Nette\Utils\ArrayHash;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class SameSiteValidatorTest extends TestCase
{

    /**
     * @dataProvider getDataForAccessAllowed
     * @param bool $isSameSite
     * @param bool $annotation
     */
    public function testAccessAllowed(bool $isSameSite, bool $annotation): void
    {
        $user = $this->mockHttpRequest($isSameSite);
        $validator = new AccessValidators\SameSiteValidator($user);

        Assert::noError(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        });
    }

    /**
     * @return mixed[]
     */
    protected function getDataForAccessAllowed(): array
    {
        return [
            [
                'isSameSite' => true,
                'annotation' => true,
            ],
            [
                'isSameSite' => true,
                'annotation' => false,
            ],
            [
                'isSameSite' => false,
                'annotation' => false,
            ],
        ];
    }

    public function testAccessDenied(): void
    {
        $user = $this->mockHttpRequest(false);
        $validator = new AccessValidators\SameSiteValidator($user);

        Assert::exception(function () use ($validator): void {
            $validator->validateAccess(true);
        }, Nette\Application\ForbiddenRequestException::class);
    }

    /**
     * @dataProvider getDataForInvalidAnnotation
     * @param mixed $annotation
     */
    public function testInvalidAnnotation($annotation): void
    {
        $user = $this->mockHttpRequest();
        $validator = new AccessValidators\SameSiteValidator($user);

        Assert::exception(function () use ($validator, $annotation): void {
            $validator->validateAccess($annotation);
        }, \InvalidArgumentException::class, 'Unexpected annotation type, bool expected.');
    }

    /**
     * @return mixed[]
     */
    protected function getDataForInvalidAnnotation(): array
    {
        return [
            [
                'annotation' => null,
            ],
            [
                'annotation' => 'foo',
            ],
            [
                'annotation' => ['foo', 'bar'],
            ],
            [
                'annotation' => ArrayHash::from(['foo' => 'bar']),
            ],
        ];
    }

    /**
     * @param bool $isSameSite
     * @return Nette\Http\Request|MockInterface
     */
    private function mockHttpRequest(bool $isSameSite = false): Nette\Http\Request
    {
        $user = Mockery::mock(Nette\Http\Request::class);
        $user->shouldReceive('isSameSite')->andReturn($isSameSite);

        return $user;
    }

}


(new SameSiteValidatorTest())->run();
