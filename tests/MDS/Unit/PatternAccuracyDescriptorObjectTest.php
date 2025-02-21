<?php

declare(strict_types=1);

namespace Webauthn\Tests\MetadataService\Unit;

use const JSON_UNESCAPED_SLASHES;
use LogicException;
use PHPUnit\Framework\TestCase;
use Webauthn\MetadataService\Statement\PatternAccuracyDescriptor;

/**
 * @internal
 */
final class PatternAccuracyDescriptorObjectTest extends TestCase
{
    /**
     * @test
     * @dataProvider validObjectData
     */
    public function validObject(
        PatternAccuracyDescriptor $object,
        int $minComplexity,
        ?int $maxRetries,
        ?int $blockSlowdown,
        string $expectedJson
    ): void {
        static::assertSame($minComplexity, $object->getMinComplexity());
        static::assertSame($maxRetries, $object->getMaxRetries());
        static::assertSame($blockSlowdown, $object->getBlockSlowdown());
        static::assertSame($expectedJson, json_encode($object, JSON_UNESCAPED_SLASHES));
    }

    public function validObjectData(): array
    {
        return [
            [new PatternAccuracyDescriptor(10), 10, null, null, '{"minComplexity":10}'],
            [
                new PatternAccuracyDescriptor(10, 50, 15),
                10,
                50,
                15,
                '{"minComplexity":10,"maxRetries":50,"blockSlowdown":15}',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider invalidObjectData
     */
    public function invalidObject(
        int $minComplexity,
        ?int $maxRetries,
        ?int $blockSlowdown,
        string $expectedMessage
    ): void {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage($expectedMessage);

        new PatternAccuracyDescriptor($minComplexity, $maxRetries, $blockSlowdown);
    }

    public function invalidObjectData(): array
    {
        return [
            [-1, null, null, 'Invalid data. The value of "minComplexity" must be a positive integer'],
            [11, -1, null, 'Invalid data. The value of "maxRetries" must be a positive integer'],
            [11, 1, -1, 'Invalid data. The value of "blockSlowdown" must be a positive integer'],
        ];
    }
}
