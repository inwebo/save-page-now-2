<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Tests\Unit\Response;

use Inwebo\SavePageNow2\Response\CaptureJobResponse;
use PHPUnit\Framework\TestCase;

final class CaptureJobResponseTest extends TestCase
{
    private const VALID_PAYLOAD = [
        'url' => 'http://brewster.kahle.org/',
        'job_id' => 'ac58789b-f3ca-48d0-9ea6-1d1225e98695',
    ];

    public function testFromArrayParsesValidPayload(): void
    {
        $response = CaptureJobResponse::fromArray(self::VALID_PAYLOAD);

        $this->assertSame('http://brewster.kahle.org/', $response->url);
        $this->assertSame('ac58789b-f3ca-48d0-9ea6-1d1225e98695', $response->jobId);
    }

    public function testFromArrayThrowsWhenJobIdIsMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CaptureJobResponse::fromArray(['url' => 'http://example.com']);
    }

    public function testFromArrayThrowsWhenUrlIsMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CaptureJobResponse::fromArray(['job_id' => 'some-id']);
    }

    public function testFromArrayThrowsWhenPayloadIsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CaptureJobResponse::fromArray([]);
    }
}
