<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2;

use Inwebo\SavePageNow2\Capture\CaptureOptions;
use Inwebo\SavePageNow2\Exception\ApiException;
use Inwebo\SavePageNow2\Exception\AuthenticationException;
use Inwebo\SavePageNow2\Exception\NetworkException;
use Inwebo\SavePageNow2\Exception\UserSessionLimitException;
use Inwebo\SavePageNow2\Response\CaptureJobResponse;
use Inwebo\SavePageNow2\Response\Status\StatusResponseInterface;
use Inwebo\SavePageNow2\Response\SystemStatusResponse;
use Inwebo\SavePageNow2\Response\UserStatusResponse;

/**
 * Contract for the Save Page Now 2 API client.
 *
 * Endpoint reference:
 *   POST https://web.archive.org/save                     → capture()
 *   GET  https://web.archive.org/save/status/{job_id}     → getStatus()
 *   POST https://web.archive.org/save/status              → getStatuses() / getOutlinksStatus()
 *   GET  https://web.archive.org/save/status/user         → getUserStatus()
 *   GET  https://web.archive.org/save/status/system       → getSystemStatus()
 *
 * @see https://docs.google.com/document/d/1Nsv52MvSjbLb2PCpHlat0gkzw0EvtSgpKHu4mk0MnrA/
 */
interface SavePageNow2Interface
{
    /**
     * Submits a URL for archiving.
     *
     * @param string              $url     the URL to archive
     * @param CaptureOptions|null $options optional capture parameters
     *
     * @throws AuthenticationException   when credentials are rejected
     * @throws UserSessionLimitException when the user has too many concurrent captures
     * @throws ApiException              on any other API-level error
     * @throws NetworkException          on transport-level failure
     */
    public function capture(string $url, ?CaptureOptions $options = null): CaptureJobResponse;

    /**
     * Polls the status of a single capture job.
     *
     * @param string $jobId the job_id returned by capture()
     *
     * @throws ApiException     on any API-level error
     * @throws NetworkException on transport-level failure
     */
    public function getStatus(string $jobId): StatusResponseInterface;

    /**
     * Polls the status of multiple capture jobs in a single request.
     *
     * @param non-empty-list<string> $jobIds
     *
     * @return array<string, StatusResponseInterface> keyed by job_id
     *
     * @throws ApiException     on any API-level error
     * @throws NetworkException on transport-level failure
     */
    public function getStatuses(array $jobIds): array;

    /**
     * Returns the status of all outlinks of a parent capture job.
     *
     * @param string $parentJobId the job_id of the parent capture (must have used capture_outlinks=1)
     *
     * @return array<string, StatusResponseInterface> keyed by job_id
     *
     * @throws ApiException     on any API-level error
     * @throws NetworkException on transport-level failure
     */
    public function getOutlinksStatus(string $parentJobId): array;

    /**
     * Returns the current session usage for the authenticated user.
     *
     * @throws AuthenticationException when credentials are rejected
     * @throws NetworkException        on transport-level failure
     */
    public function getUserStatus(): UserStatusResponse;

    /**
     * Returns the overall health of the SPN2 service.
     *
     * @throws NetworkException on transport-level failure
     */
    public function getSystemStatus(): SystemStatusResponse;
}
