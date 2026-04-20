<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Tests\Unit\Response;

use Inwebo\SavePageNow2\Response\JobStatus;
use Inwebo\SavePageNow2\Response\Status\SuccessStatusResponse;
use PHPUnit\Framework\TestCase;

final class SuccessStatusResponseTest extends TestCase
{
    private const PAYLOAD = [
        'status' => 'success',
        'job_id' => 'ac58789b-f3ca-48d0-9ea6-1d1225e98695',
        'original_url' => 'http://brewster.kahle.org/',
        'screenshot' => 'http://web.archive.org/screenshot/http://brewster.kahle.org/',
        'timestamp' => '20180326070330',
        'duration_sec' => 6.203,
        'resources' => [
            'http://brewster.kahle.org/',
            'http://brewster.kahle.org/favicon.ico',
        ],
        'outlinks' => [
            'https://archive.org/' => 'xxxxxx89b-f3ca-48d0-9ea6-1d1225e98695',
            'https://other.com' => 'yyyy89b-f3ca-48d0-9ea6-1d1225e98695',
        ],
    ];

    public function testFromArrayParsesAllFields(): void
    {
        $response = SuccessStatusResponse::fromArray(self::PAYLOAD);

        $this->assertSame(JobStatus::Success, $response->getStatus());
        $this->assertSame('ac58789b-f3ca-48d0-9ea6-1d1225e98695', $response->getJobId());
        $this->assertSame('http://brewster.kahle.org/', $response->getOriginalUrl());
        $this->assertSame('20180326070330', $response->getTimestamp());
        $this->assertSame(6.203, $response->getDurationSec());
        $this->assertCount(2, $response->getResources());
        $this->assertCount(2, $response->getOutlinks());
        $this->assertSame(
            'http://web.archive.org/screenshot/http://brewster.kahle.org/',
            $response->getScreenshot()
        );
    }

    public function testGetWaybackUrlBuildsCorrectUrl(): void
    {
        $response = SuccessStatusResponse::fromArray(self::PAYLOAD);

        $this->assertSame(
            'https://web.archive.org/web/20180326070330/http://brewster.kahle.org/',
            $response->getWaybackUrl()
        );
    }

    public function testScreenshotIsNullWhenNotPresent(): void
    {
        $payload = self::PAYLOAD;
        unset($payload['screenshot']);

        $response = SuccessStatusResponse::fromArray($payload);

        $this->assertNull($response->getScreenshot());
    }

    public function testOutlinksDefaultsToEmptyArray(): void
    {
        $payload = self::PAYLOAD;
        unset($payload['outlinks']);

        $response = SuccessStatusResponse::fromArray($payload);

        $this->assertSame([], $response->getOutlinks());
    }

    public function testResourcesDefaultsToEmptyArray(): void
    {
        $response = SuccessStatusResponse::fromArray([
            'status' => 'success',
            'job_id' => 'some-id',
            'original_url' => 'http://example.com',
            'timestamp' => '20180101000000',
        ]);

        $this->assertSame([], $response->getResources());
    }
}
