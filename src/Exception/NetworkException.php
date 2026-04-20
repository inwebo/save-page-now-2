<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Exception;

/**
 * Thrown when the HTTP transport layer fails (connection refused, timeout, DNS failure…).
 */
class NetworkException extends SavePageNowException
{
}
