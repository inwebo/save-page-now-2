<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Response\Status;

use Inwebo\SavePageNow2\Response\JobStatus;

/**
 * Common interface for all job-status responses.
 */
interface StatusResponseInterface
{
    public function getJobId(): string;

    public function getStatus(): JobStatus;

    /** @return list<string> */
    public function getResources(): array;
}
