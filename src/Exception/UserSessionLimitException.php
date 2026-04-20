<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Exception;

/**
 * Thrown when the user has reached the maximum number of concurrent capture sessions (12 for
 * authenticated users, 6 for anonymous users).
 *
 * Corresponds to status_ext = "error:user-session-limit".
 */
class UserSessionLimitException extends SavePageNowException
{
}
