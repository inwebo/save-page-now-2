<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Response\Status;

use Inwebo\SavePageNow2\Response\JobStatus;

/**
 * Represents a failed capture.
 *
 * Example payload:
 * {
 *   "status": "error",
 *   "exception": "[Errno -2] Name or service not known",
 *   "status_ext": "error:invalid-host-resolution",
 *   "job_id": "2546c79b-ec70-4bec-b78b-1941c42a6374",
 *   "message": "Couldn't resolve host for http://example5123.com.",
 *   "resources": []
 * }
 */
final class ErrorStatusResponse implements StatusResponseInterface
{
    /**
     * @param list<string> $resources
     */
    public function __construct(
        private string $jobId,
        private string $message,
        private string $statusExt,
        private string $exception,
        private array $resources = [],
    ) {
    }

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function getStatus(): JobStatus
    {
        return JobStatus::Error;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Machine-readable error code, e.g. "error:invalid-host-resolution".
     *
     * @see https://gist.github.com/regstuff/82e690db2f1d91ba59f6681c1abad6cf for the full list
     */
    public function getStatusExt(): string
    {
        return $this->statusExt;
    }

    public function getException(): string
    {
        return $this->exception;
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
            message: (string) ($data['message'] ?? ''),
            statusExt: (string) ($data['status_ext'] ?? ''),
            exception: (string) ($data['exception'] ?? ''),
            resources: (array) ($data['resources'] ?? []),
        );
    }
}
