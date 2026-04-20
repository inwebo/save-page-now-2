<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2;

use Inwebo\SavePageNow2\Auth\AuthInterface;
use Inwebo\SavePageNow2\Capture\CaptureOptions;
use Inwebo\SavePageNow2\Exception\ApiException;
use Inwebo\SavePageNow2\Exception\AuthenticationException;
use Inwebo\SavePageNow2\Exception\NetworkException;
use Inwebo\SavePageNow2\Exception\UserSessionLimitException;
use Inwebo\SavePageNow2\Response\CaptureJobResponse;
use Inwebo\SavePageNow2\Response\Status\StatusResponseFactory;
use Inwebo\SavePageNow2\Response\Status\StatusResponseInterface;
use Inwebo\SavePageNow2\Response\SystemStatusResponse;
use Inwebo\SavePageNow2\Response\UserStatusResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Symfony HttpClient implementation of the SPN2 client.
 *
 * Usage:
 *   $client = new SavePageNow2Client(
 *       HttpClient::create(),
 *       new S3Credentials('my-access-key', 'my-secret'),
 *   );
 *
 *   $job     = $client->capture('https://example.com');
 *   $status  = $client->getStatus($job->jobId);
 */
final class SavePageNow2Client implements SavePageNow2Interface
{
    private const BASE_URL = 'https://web.archive.org/save';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly AuthInterface $auth,
    ) {
    }

    // -------------------------------------------------------------------------
    // Capture
    // -------------------------------------------------------------------------

    public function capture(string $url, ?CaptureOptions $options = null): CaptureJobResponse
    {
        $body = array_merge(['url' => $url], $options?->toPostParams() ?? []);

        $data = $this->post(self::BASE_URL, $body);

        if (!isset($data['job_id'])) {
            throw new ApiException(sprintf('Unexpected capture response (missing job_id): %s', json_encode($data)));
        }

        return CaptureJobResponse::fromArray($data);
    }

    // -------------------------------------------------------------------------
    // Status
    // -------------------------------------------------------------------------

    public function getStatus(string $jobId): StatusResponseInterface
    {
        $data = $this->get(sprintf('%s/status/%s', self::BASE_URL, $jobId));

        return StatusResponseFactory::fromArray($data);
    }

    public function getStatuses(array $jobIds): array
    {
        $data = $this->post(
            sprintf('%s/status', self::BASE_URL),
            ['job_ids' => implode(',', $jobIds)],
        );

        return $this->parseMultipleStatuses($data);
    }

    public function getOutlinksStatus(string $parentJobId): array
    {
        $data = $this->post(
            sprintf('%s/status', self::BASE_URL),
            ['job_id_outlinks' => $parentJobId],
        );

        return $this->parseMultipleStatuses($data);
    }

    // -------------------------------------------------------------------------
    // User / system health
    // -------------------------------------------------------------------------

    public function getUserStatus(): UserStatusResponse
    {
        // Add a random _t param to bypass stale cache (recommended by the API docs)
        $data = $this->get(
            sprintf('%s/status/user?_t=%d', self::BASE_URL, random_int(0, PHP_INT_MAX))
        );

        return UserStatusResponse::fromArray($data);
    }

    public function getSystemStatus(): SystemStatusResponse
    {
        $data = $this->get(sprintf('%s/status/system', self::BASE_URL));

        return SystemStatusResponse::fromArray($data);
    }

    // -------------------------------------------------------------------------
    // HTTP helpers
    // -------------------------------------------------------------------------

    /**
     * @param array<string, mixed> $body
     *
     * @return array<string, mixed>
     */
    private function post(string $url, array $body): array
    {
        try {
            $response = $this->httpClient->request('POST', $url, [
                'headers' => array_merge(
                    $this->auth->getHeaders(),
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                ),
                'body' => $body,
            ]);

            return $this->decodeResponse($response);
        } catch (TransportExceptionInterface $e) {
            throw new NetworkException($e->getMessage(), previous: $e);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function get(string $url): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => array_merge(
                    $this->auth->getHeaders(),
                    ['Accept' => 'application/json'],
                ),
            ]);

            return $this->decodeResponse($response);
        } catch (TransportExceptionInterface $e) {
            throw new NetworkException($e->getMessage(), previous: $e);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(\Symfony\Contracts\HttpClient\ResponseInterface $response): array
    {
        try {
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(throw: false);
            $data = json_decode($content, associative: true, flags: JSON_THROW_ON_ERROR);

            if (401 === $statusCode) {
                throw new AuthenticationException($data['message'] ?? 'Authentication failed.');
            }

            if (502 === $statusCode) {
                throw new ApiException('SPN2 service returned a critical error (HTTP 502).');
            }

            // status_ext-level errors that deserve dedicated exceptions
            if (isset($data['status_ext'])) {
                match ($data['status_ext']) {
                    'error:user-session-limit' => throw new UserSessionLimitException($data['message'] ?? 'User session limit reached.'),
                    'error:unauthorized' => throw new AuthenticationException($data['message'] ?? 'Unauthorized.'),
                    default => null,
                };
            }

            return $data;
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            throw new ApiException($e->getMessage(), previous: $e);
        } catch (\JsonException $e) {
            throw new ApiException('Could not decode the SPN2 API response as JSON.', previous: $e);
        }
    }

    /**
     * The multi-status endpoint returns an object keyed by job_id.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, StatusResponseInterface>
     */
    private function parseMultipleStatuses(array $data): array
    {
        $result = [];

        foreach ($data as $jobId => $jobData) {
            if (!is_array($jobData)) {
                continue;
            }
            // Ensure the nested payload carries the job_id
            $jobData['job_id'] ??= $jobId;
            $result[(string) $jobId] = StatusResponseFactory::fromArray($jobData);
        }

        return $result;
    }
}
