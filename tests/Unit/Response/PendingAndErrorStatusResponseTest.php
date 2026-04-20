<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Tests\Unit\Response;

use Inwebo\SavePageNow2\Response\JobStatus;
use Inwebo\SavePageNow2\Response\Status\ErrorStatusResponse;
use Inwebo\SavePageNow2\Response\Status\PendingStatusResponse;
use PHPUnit\Framework\TestCase;

final class PendingAndErrorStatusResponseTest extends TestCase
{
    public function testFromArrayParsesCorrectly(): void
    {
        $response = PendingStatusResponse::fromArray([
            'status' => 'pending',
            'job_id' => 'e70f33c7-9eca-4c88-826d-26930564d7c8',
            'resources' => [
                'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
            ],
        ]);

        $this->assertSame(JobStatus::Pending, $response->getStatus());
        $this->assertSame('e70f33c7-9eca-4c88-826d-26930564d7c8', $response->getJobId());
        $this->assertCount(1, $response->getResources());
    }

    public function testResourcesDefaultsToEmptyArray(): void
    {
        $response = PendingStatusResponse::fromArray([
            'status' => 'pending',
            'job_id' => 'some-id',
        ]);

        $this->assertSame([], $response->getResources());
    }
}

final class ErrorStatusResponseTest extends TestCase
{
    private const PAYLOAD = [
        'status' => 'error',
        'exception' => '[Errno -2] Name or service not known',
        'status_ext' => 'error:invalid-host-resolution',
        'job_id' => '2546c79b-ec70-4bec-b78b-1941c42a6374',
        'message' => "Couldn't resolve host for http://example5123.com.",
        'resources' => [],
    ];

    public function testFromArrayParsesAllFields(): void
    {
        $response = ErrorStatusResponse::fromArray(self::PAYLOAD);

        $this->assertSame(JobStatus::Error, $response->getStatus());
        $this->assertSame('2546c79b-ec70-4bec-b78b-1941c42a6374', $response->getJobId());
        $this->assertSame('error:invalid-host-resolution', $response->getStatusExt());
        $this->assertSame('[Errno -2] Name or service not known', $response->getException());
        $this->assertStringContainsString('resolve host', $response->getMessage());
        $this->assertSame([], $response->getResources());
    }

    public function testMissingFieldsDefaultToEmptyString(): void
    {
        $response = ErrorStatusResponse::fromArray(['status' => 'error']);

        $this->assertSame('', $response->getJobId());
        $this->assertSame('', $response->getStatusExt());
        $this->assertSame('', $response->getException());
        $this->assertSame('', $response->getMessage());
    }
}
