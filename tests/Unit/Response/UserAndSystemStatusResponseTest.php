<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Tests\Unit\Response;

use Inwebo\SavePageNow2\Response\SystemStatusResponse;
use Inwebo\SavePageNow2\Response\UserStatusResponse;
use PHPUnit\Framework\TestCase;

final class UserAndSystemStatusResponseTest extends TestCase
{
    public function testFromArrayParsesAvailableAndProcessing(): void
    {
        $response = UserStatusResponse::fromArray(['available' => 12, 'processing' => 3]);

        $this->assertSame(12, $response->available);
        $this->assertSame(3, $response->processing);
    }

    public function testHasAvailableSlotsReturnsTrueWhenPositive(): void
    {
        $response = UserStatusResponse::fromArray(['available' => 5, 'processing' => 7]);

        $this->assertTrue($response->hasAvailableSlots());
    }

    public function testHasAvailableSlotsReturnsFalseWhenZero(): void
    {
        $response = UserStatusResponse::fromArray(['available' => 0, 'processing' => 12]);

        $this->assertFalse($response->hasAvailableSlots());
    }

    public function testMissingKeysDefaultToZero(): void
    {
        $response = UserStatusResponse::fromArray([]);

        $this->assertSame(0, $response->available);
        $this->assertSame(0, $response->processing);
    }
}

final class SystemStatusResponseTest extends TestCase
{
    public function testIsOkReturnsTrueForOkStatus(): void
    {
        $response = SystemStatusResponse::fromArray(['status' => 'ok']);

        $this->assertTrue($response->isOk());
        $this->assertFalse($response->isOverloaded());
    }

    public function testIsOverloadedReturnsTrueForNonOkStatus(): void
    {
        $response = SystemStatusResponse::fromArray([
            'status' => 'Save Page Now servers are temporarily overloaded. Your captures may be delayed.',
        ]);

        $this->assertTrue($response->isOverloaded());
        $this->assertFalse($response->isOk());
    }

    public function testStatusFieldIsAccessible(): void
    {
        $response = SystemStatusResponse::fromArray(['status' => 'ok']);

        $this->assertSame('ok', $response->status);
    }
}
