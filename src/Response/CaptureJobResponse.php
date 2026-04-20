<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Response;

/**
 * Response returned by the initial capture request.
 *
 * Example payload:
 *   {"url":"http://example.com/","job_id":"ac58789b-f3ca-48d0-9ea6-1d1225e98695"}
 */
final class CaptureJobResponse
{
    public function __construct(
        public readonly string $url,
        public readonly string $jobId,
    ) {
    }

    /**
     * @param array{url: string, job_id: string} $data
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['url'], $data['job_id'])) {
            throw new \InvalidArgumentException('Capture response must contain "url" and "job_id" keys.');
        }

        return new self(
            url: $data['url'],
            jobId: $data['job_id'],
        );
    }
}
