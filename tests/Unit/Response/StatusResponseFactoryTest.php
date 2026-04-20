<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Tests\Unit\Response;

use Inwebo\SavePageNow2\Exception\ApiException;
use Inwebo\SavePageNow2\Response\JobStatus;
use Inwebo\SavePageNow2\Response\Status\ErrorStatusResponse;
use Inwebo\SavePageNow2\Response\Status\PendingStatusResponse;
use Inwebo\SavePageNow2\Response\Status\StatusResponseFactory;
use Inwebo\SavePageNow2\Response\Status\SuccessStatusResponse;
use PHPUnit\Framework\TestCase;

final class StatusResponseFactoryTest extends TestCase
{
    public function testDispatchesToPendingResponse(): void
    {
        $response = StatusResponseFactory::fromArray([
            'status' => 'pending',
            'job_id' => 'some-id',
        ]);

        $this->assertInstanceOf(PendingStatusResponse::class, $response);
        $this->assertSame(JobStatus::Pending, $response->getStatus());
    }

    public function testDispatchesToSuccessResponse(): void
    {
        $response = StatusResponseFactory::fromArray([
            'status' => 'success',
            'job_id' => 'some-id',
            'original_url' => 'http://example.com',
            'timestamp' => '20180101000000',
        ]);

        $this->assertInstanceOf(SuccessStatusResponse::class, $response);
        $this->assertSame(JobStatus::Success, $response->getStatus());
    }

    public function testDispatchesToErrorResponse(): void
    {
        $response = StatusResponseFactory::fromArray([
            'status' => 'error',
            'job_id' => 'some-id',
            'message' => 'Something went wrong',
        ]);

        $this->assertInstanceOf(ErrorStatusResponse::class, $response);
        $this->assertSame(JobStatus::Error, $response->getStatus());
    }

    public function testThrowsApiExceptionOnUnknownStatus(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Unknown job status "running"');

        StatusResponseFactory::fromArray(['status' => 'running']);
    }

    public function testThrowsApiExceptionWhenStatusIsMissing(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('missing the "status" field');

        StatusResponseFactory::fromArray(['job_id' => 'some-id']);
    }
}
