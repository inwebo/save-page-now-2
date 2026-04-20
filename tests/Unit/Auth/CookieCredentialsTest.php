<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Tests\Unit\Auth;

use Inwebo\SavePageNow2\Auth\CookieCredentials;
use PHPUnit\Framework\TestCase;

final class CookieCredentialsTest extends TestCase
{
    public function testGetHeadersReturnsCorrectCookieHeader(): void
    {
        $credentials = new CookieCredentials('user%40archive.org', 'AAAAAAAAAA');

        $headers = $credentials->getHeaders();

        $this->assertArrayHasKey('Cookie', $headers);
        $this->assertStringContainsString('logged-in-sig=AAAAAAAAAA', $headers['Cookie']);
        $this->assertStringContainsString('logged-in-user=user%40archive.org', $headers['Cookie']);
    }

    public function testEmptyUserThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('logged-in-user');

        new CookieCredentials('', 'sig');
    }

    public function testEmptySigThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('logged-in-sig');

        new CookieCredentials('user', '');
    }
}
