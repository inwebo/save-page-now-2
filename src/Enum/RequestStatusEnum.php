<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Enum;

enum RequestStatusEnum: string
{
    case SUCCESS = 'success';
    case PENDING = 'pending';
    case ERROR = 'error';
    /**
     * System status: Everything is fine.
     */
    case OK = 'ok';
    /**
     * System status: If there is a critical problem, there will be an HTTP status=502 response.
     */
    case CRITICAL_PROBLEM = 'critical';
    /**
     * System status: To be clear, SPN will still work fine in this case, besides some delays.
     */
    case OVERLOADED = 'overloaded';
}
