<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Tests\Unit\Auth;

use Inwebo\SavePageNow2\Auth\S3Credentials;
use PHPUnit\Framework\TestCase;

final class S3CredentialsTest extends TestCase
{
    public function testGetHeadersReturnsCorrectAuthorizationHeader(): void
    {
        $credentials = new S3Credentials('my-access-key', 'my-secret');

        $headers = $credentials->getHeaders();

        $this->assertSame(['Authorization' => 'LOW my-access-key:my-secret'], $headers);
    }

    public function testAccessorsReturnConstructorValues(): void
    {
        $credentials = new S3Credentials('access', 'secret');

        $this->assertSame('access', $credentials->getAccessKey());
        $this->assertSame('secret', $credentials->getSecretKey());
    }

    public function testEmptyAccessKeyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('access key');

        new S3Credentials('', 'secret');
    }

    public function testEmptySecretKeyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('secret key');

        new S3Credentials('access', '');
    }

    public function testBlankAccessKeyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new S3Credentials('   ', 'secret');
    }
}
