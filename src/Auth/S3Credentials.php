<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Auth;

/**
 * S3-key based authentication (recommended method).
 *
 * Obtain your keys at https://archive.org/account/s3.php
 *
 * Produces the header: Authorization: LOW <accessKey>:<secretKey>
 */
final class S3Credentials implements AuthInterface
{
    public function __construct(
        private string $accessKey,
        private string $secretKey,
    ) {
        if ('' === trim($this->accessKey)) {
            throw new \InvalidArgumentException('The S3 access key must not be empty.');
        }
        if ('' === trim($this->secretKey)) {
            throw new \InvalidArgumentException('The S3 secret key must not be empty.');
        }
    }

    public function getAccessKey(): string
    {
        return $this->accessKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getHeaders(): array
    {
        return [
            'Authorization' => sprintf('LOW %s:%s', $this->accessKey, $this->secretKey),
        ];
    }
}
