<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Exception\Transport;

use Symfony\Component\HttpClient\Exception\TransportException;

class NotLoggedException extends TransportException
{
    public function __construct(string $message = 'You need to be logged in to use Save Page Now. Your access keys can be find here  : https://archive.org/account/s3.php', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
