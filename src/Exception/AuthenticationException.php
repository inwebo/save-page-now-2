<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Exception;

/**
 * Thrown when the API rejects the provided credentials (HTTP 401 / status_ext=error:unauthorized).
 */
class AuthenticationException extends SavePageNowException
{
}
