<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Response\Status;

use Inwebo\SavePageNow2\Exception\ApiException;
use Inwebo\SavePageNow2\Response\JobStatus;

/**
 * Converts the raw decoded JSON from a status endpoint call into
 * the appropriate StatusResponseInterface implementation.
 */
final class StatusResponseFactory
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws ApiException when the status value is unknown or missing
     */
    public static function fromArray(array $data): StatusResponseInterface
    {
        $rawStatus = $data['status'] ?? null;

        if (null === $rawStatus) {
            throw new ApiException('Status response is missing the "status" field.');
        }

        $status = JobStatus::tryFrom((string) $rawStatus);

        if (null === $status) {
            throw new ApiException(sprintf('Unknown job status "%s".', $rawStatus));
        }

        return match ($status) {
            JobStatus::Pending => PendingStatusResponse::fromArray($data),
            JobStatus::Success => SuccessStatusResponse::fromArray($data),
            JobStatus::Error => ErrorStatusResponse::fromArray($data),
        };
    }
}
