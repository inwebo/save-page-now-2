<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Response\Status;

use Inwebo\SavePageNow2\Response\JobStatus;

/**
 * Represents a successfully completed capture.
 *
 * Example payload:
 * {
 *   "status": "success",
 *   "job_id": "ac58789b-...",
 *   "original_url": "http://example.com/",
 *   "screenshot": "http://web.archive.org/screenshot/http://example.com/",
 *   "timestamp": "20180326070330",
 *   "duration_sec": 6.203,
 *   "resources": ["http://example.com/", ...],
 *   "outlinks": {"https://other.com": "yyy-job-id"}
 * }
 */
final class SuccessStatusResponse implements StatusResponseInterface
{
    /**
     * @param list<string>                                        $resources
     * @param array<string, string|array{timestamp: string|null}> $outlinks
     */
    public function __construct(
        private string $jobId,
        private string $originalUrl,
        private string $timestamp,
        private float $durationSec,
        private array $resources = [],
        private array $outlinks = [],
        private ?string $screenshot = null,
    ) {
    }

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function getStatus(): JobStatus
    {
        return JobStatus::Success;
    }

    public function getOriginalUrl(): string
    {
        return $this->originalUrl;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function getDurationSec(): float
    {
        return $this->durationSec;
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    public function getOutlinks(): array
    {
        return $this->outlinks;
    }

    public function getScreenshot(): ?string
    {
        return $this->screenshot;
    }

    /**
     * Returns the canonical Wayback Machine URL for this capture.
     */
    public function getWaybackUrl(): string
    {
        return sprintf(
            'https://web.archive.org/web/%s/%s',
            $this->timestamp,
            $this->originalUrl,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            jobId: (string) ($data['job_id'] ?? ''),
            originalUrl: (string) ($data['original_url'] ?? ''),
            timestamp: (string) ($data['timestamp'] ?? ''),
            durationSec: (float) ($data['duration_sec'] ?? 0.0),
            resources: (array) ($data['resources'] ?? []),
            outlinks: (array) ($data['outlinks'] ?? []),
            screenshot: isset($data['screenshot']) ? (string) $data['screenshot'] : null,
        );
    }
}
