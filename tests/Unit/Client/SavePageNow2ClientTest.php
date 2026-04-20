<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Tests\Unit\Client;

use Inwebo\SavePageNow2\Auth\S3Credentials;
use Inwebo\SavePageNow2\Capture\CaptureOptionsBuilder;
use Inwebo\SavePageNow2\Exception\ApiException;
use Inwebo\SavePageNow2\Exception\AuthenticationException;
use Inwebo\SavePageNow2\Exception\UserSessionLimitException;
use Inwebo\SavePageNow2\Response\JobStatus;
use Inwebo\SavePageNow2\Response\Status\ErrorStatusResponse;
use Inwebo\SavePageNow2\Response\Status\PendingStatusResponse;
use Inwebo\SavePageNow2\Response\Status\SuccessStatusResponse;
use Inwebo\SavePageNow2\SavePageNow2Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class SavePageNow2ClientTest extends TestCase
{
    private S3Credentials $credentials;

    protected function setUp(): void
    {
        $this->credentials = new S3Credentials('test-access', 'test-secret');
    }

    // -------------------------------------------------------------------------
    // capture()
    // -------------------------------------------------------------------------

    public function testCaptureReturnsCaptureJobResponse(): void
    {
        $payload = json_encode([
            'url' => 'http://example.com/',
            'job_id' => 'ac58789b-f3ca-48d0-9ea6-1d1225e98695',
        ]);

        $client = $this->makeClient(new MockResponse($payload, ['http_code' => 200]));

        $response = $client->capture('http://example.com/');

        $this->assertSame('http://example.com/', $response->url);
        $this->assertSame('ac58789b-f3ca-48d0-9ea6-1d1225e98695', $response->jobId);
    }

    public function testCaptureForwardsOptions(): void
    {
        $payload = json_encode(['url' => 'http://example.com/', 'job_id' => 'abc-123']);
        $responses = [new MockResponse($payload)];
        $httpClient = new MockHttpClient($responses);

        $client = new SavePageNow2Client($httpClient, $this->credentials);
        $options = (new CaptureOptionsBuilder())
            ->withCaptureAll()
            ->withSkipFirstArchive()
            ->withJsBehaviorTimeout(0)
            ->build();

        $client->capture('http://example.com/', $options);

        $request = $responses[0]->getRequestOptions();
        $body = $request['body'] ?? '';

        $this->assertStringContainsString('capture_all=1', $body);
        $this->assertStringContainsString('skip_first_archive=1', $body);
        $this->assertStringContainsString('js_behavior_timeout=0', $body);
    }

    public function testCaptureThrowsApiExceptionWhenJobIdIsMissing(): void
    {
        $client = $this->makeClient(new MockResponse(json_encode(['url' => 'http://example.com/'])));

        $this->expectException(ApiException::class);

        $client->capture('http://example.com/');
    }

    public function testCaptureThrowsAuthenticationExceptionOn401(): void
    {
        $payload = json_encode(['message' => 'Forbidden']);
        $client = $this->makeClient(new MockResponse($payload, ['http_code' => 401]));

        $this->expectException(AuthenticationException::class);

        $client->capture('http://example.com/');
    }

    public function testCaptureThrowsUserSessionLimitException(): void
    {
        $payload = json_encode([
            'status' => 'error',
            'status_ext' => 'error:user-session-limit',
            'message' => 'User has reached the limit of concurrent active capture sessions.',
            'job_id' => '',
        ]);
        $client = $this->makeClient(new MockResponse($payload, ['http_code' => 200]));

        $this->expectException(UserSessionLimitException::class);

        $client->capture('http://example.com/');
    }

    public function testCaptureThrowsApiExceptionOn502(): void
    {
        $client = $this->makeClient(new MockResponse('', ['http_code' => 502]));

        $this->expectException(ApiException::class);

        $client->capture('http://example.com/');
    }

    public function testCaptureAddsAuthorizationHeader(): void
    {
        $payload = json_encode(['url' => 'http://example.com/', 'job_id' => 'abc-123']);
        $responses = [new MockResponse($payload)];
        $httpClient = new MockHttpClient($responses);
        $client = new SavePageNow2Client($httpClient, $this->credentials);

        $client->capture('http://example.com/');

        $headers = $responses[0]->getRequestOptions()['normalized_headers'] ?? [];
        $authHeader = implode('', $headers['authorization'] ?? []);

        $this->assertStringContainsString('LOW test-access:test-secret', $authHeader);
    }

    // -------------------------------------------------------------------------
    // getStatus()
    // -------------------------------------------------------------------------

    public function testGetStatusReturnsPendingResponse(): void
    {
        $payload = json_encode([
            'status' => 'pending',
            'job_id' => 'some-id',
            'resources' => [],
        ]);
        $client = $this->makeClient(new MockResponse($payload));

        $status = $client->getStatus('some-id');

        $this->assertInstanceOf(PendingStatusResponse::class, $status);
        $this->assertSame(JobStatus::Pending, $status->getStatus());
    }

    public function testGetStatusReturnsSuccessResponse(): void
    {
        $payload = json_encode([
            'status' => 'success',
            'job_id' => 'some-id',
            'original_url' => 'http://example.com/',
            'timestamp' => '20240101120000',
            'duration_sec' => 3.5,
            'resources' => [],
        ]);
        $client = $this->makeClient(new MockResponse($payload));

        $status = $client->getStatus('some-id');

        $this->assertInstanceOf(SuccessStatusResponse::class, $status);
        $this->assertSame(JobStatus::Success, $status->getStatus());
    }

    public function testGetStatusReturnsErrorResponse(): void
    {
        $payload = json_encode([
            'status' => 'error',
            'job_id' => 'some-id',
            'status_ext' => 'error:not-found',
            'message' => 'Target URL not found (status=404).',
            'exception' => '',
            'resources' => [],
        ]);
        $client = $this->makeClient(new MockResponse($payload));

        $status = $client->getStatus('some-id');

        $this->assertInstanceOf(ErrorStatusResponse::class, $status);
        $this->assertSame(JobStatus::Error, $status->getStatus());
        $this->assertSame('error:not-found', $status->getStatusExt());
    }

    // -------------------------------------------------------------------------
    // getStatuses()
    // -------------------------------------------------------------------------

    public function testGetStatusesReturnsMapKeyedByJobId(): void
    {
        $payload = json_encode([
            'job-id-1' => ['status' => 'success', 'job_id' => 'job-id-1', 'original_url' => 'http://a.com', 'timestamp' => '20240101'],
            'job-id-2' => ['status' => 'pending', 'job_id' => 'job-id-2'],
        ]);
        $client = $this->makeClient(new MockResponse($payload));

        $statuses = $client->getStatuses(['job-id-1', 'job-id-2']);

        $this->assertArrayHasKey('job-id-1', $statuses);
        $this->assertArrayHasKey('job-id-2', $statuses);
        $this->assertInstanceOf(SuccessStatusResponse::class, $statuses['job-id-1']);
        $this->assertInstanceOf(PendingStatusResponse::class, $statuses['job-id-2']);
    }

    // -------------------------------------------------------------------------
    // getUserStatus()
    // -------------------------------------------------------------------------

    public function testGetUserStatusReturnsCorrectValues(): void
    {
        $payload = json_encode(['available' => 10, 'processing' => 2]);
        $client = $this->makeClient(new MockResponse($payload));

        $status = $client->getUserStatus();

        $this->assertSame(10, $status->available);
        $this->assertSame(2, $status->processing);
        $this->assertTrue($status->hasAvailableSlots());
    }

    // -------------------------------------------------------------------------
    // getSystemStatus()
    // -------------------------------------------------------------------------

    public function testGetSystemStatusIsOk(): void
    {
        $client = $this->makeClient(new MockResponse(json_encode(['status' => 'ok'])));

        $status = $client->getSystemStatus();

        $this->assertTrue($status->isOk());
    }

    public function testGetSystemStatusIsOverloaded(): void
    {
        $payload = json_encode([
            'status' => 'Save Page Now servers are temporarily overloaded.',
        ]);
        $client = $this->makeClient(new MockResponse($payload));

        $status = $client->getSystemStatus();

        $this->assertTrue($status->isOverloaded());
    }

    // -------------------------------------------------------------------------
    // getOutlinksStatus()
    // -------------------------------------------------------------------------

    public function testGetOutlinksStatusReturnsMapKeyedByJobId(): void
    {
        $payload = json_encode([
            'outlink-job-1' => ['status' => 'success', 'job_id' => 'outlink-job-1', 'original_url' => 'http://b.com', 'timestamp' => '20240101'],
            'outlink-job-2' => ['status' => 'error',   'job_id' => 'outlink-job-2', 'message' => 'Not found', 'status_ext' => 'error:not-found'],
        ]);
        $client = $this->makeClient(new MockResponse($payload));

        $statuses = $client->getOutlinksStatus('parent-job-id');

        $this->assertCount(2, $statuses);
        $this->assertInstanceOf(SuccessStatusResponse::class, $statuses['outlink-job-1']);
        $this->assertInstanceOf(ErrorStatusResponse::class, $statuses['outlink-job-2']);
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function makeClient(MockResponse $response): SavePageNow2Client
    {
        return new SavePageNow2Client(
            new MockHttpClient($response),
            $this->credentials,
        );
    }
}
