<?php

declare(strict_types=1);

namespace Webauthn\Tests\Unit7;

use ParagonIE\ConstantTime\Base64UrlSafe;
use PHPUnit\Framework\TestCase;
use Webauthn\CollectedClientData;
use Webauthn\TokenBinding\TokenBinding;

/**
 * @internal
 */
final class CollectedClientDataTest extends TestCase
{
    /**
     * @test
     */
    public function anCollectedClientDataCanBeCreatedAndValueAccessed(): void
    {
        $collectedClientData = new CollectedClientData(
            'raw_data',
            [
                'type' => 'type',
                'origin' => 'origin',
                'challenge' => Base64UrlSafe::encodeUnpadded('challenge'),
                'extensions' => 'extensions',
                'tokenBinding' => [
                    'status' => 'present',
                    'id' => Base64UrlSafe::encodeUnpadded('id'),
                ],
            ]
        );

        static::assertSame('raw_data', $collectedClientData->getRawData());
        static::assertSame('origin', $collectedClientData->getOrigin());
        static::assertSame('challenge', $collectedClientData->getChallenge());
        static::assertInstanceOf(TokenBinding::class, $collectedClientData->getTokenBinding());
        static::assertSame('id', $collectedClientData->getTokenBinding()->getId());
        static::assertSame('present', $collectedClientData->getTokenBinding()->getStatus());
        static::assertSame('type', $collectedClientData->getType());
        static::assertSame(
            ['type', 'origin', 'challenge', 'extensions', 'tokenBinding'],
            $collectedClientData->all()
        );
        static::assertTrue($collectedClientData->has('extensions'));
        static::assertSame('extensions', $collectedClientData->get('extensions'));
    }
}
