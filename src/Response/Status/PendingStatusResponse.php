<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Response\Status;

use Inwebo\SavePageNow2\Response\JobStatus;

/**
 * Represents a capture that is still in progress.
 *
 * Example payload:
 *   {"status":"pending","job_id":"e70f33c7-...","resources":["https://..."]}
 */
final class PendingStatusResponse implements StatusResponseInterface
{
    /**
     * @param list<string> $resources
     */
    public function __construct(
        private string $jobId,
        private array $resources = [],
    ) {
    }

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function getStatus(): JobStatus
    {
        return JobStatus::Pending;
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            jobId: (string) ($data['job_id'] ?? ''),
            resources: (array) ($data['resources'] ?? []),
        );
    }
}
