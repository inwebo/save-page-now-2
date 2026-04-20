<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Response;

enum JobStatus: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Error = 'error';
}
