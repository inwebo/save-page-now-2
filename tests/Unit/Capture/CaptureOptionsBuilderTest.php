<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Tests\Unit\Capture;

use Inwebo\SavePageNow2\Capture\CaptureOptions;
use Inwebo\SavePageNow2\Capture\CaptureOptionsBuilder;
use PHPUnit\Framework\TestCase;

final class CaptureOptionsBuilderTest extends TestCase
{
    public function testDefaultBuildProducesNoPostParams(): void
    {
        $options = (new CaptureOptionsBuilder())->build();

        $this->assertSame([], $options->toPostParams());
    }

    public function testCaptureAllOption(): void
    {
        $options = (new CaptureOptionsBuilder())->withCaptureAll()->build();

        $this->assertSame(['capture_all' => 1], $options->toPostParams());
    }

    public function testCaptureOutlinksOption(): void
    {
        $options = (new CaptureOptionsBuilder())->withCaptureOutlinks()->build();

        $this->assertArrayHasKey('capture_outlinks', $options->toPostParams());
    }

    public function testJsBehaviorTimeoutOption(): void
    {
        $options = (new CaptureOptionsBuilder())->withJsBehaviorTimeout(0)->build();

        $this->assertSame(['js_behavior_timeout' => 0], $options->toPostParams());
    }

    public function testIfNotArchivedWithinOption(): void
    {
        $options = (new CaptureOptionsBuilder())->withIfNotArchivedWithin('3d 5h')->build();

        $this->assertSame(['if_not_archived_within' => '3d 5h'], $options->toPostParams());
    }

    public function testTargetCredentialsOption(): void
    {
        $options = (new CaptureOptionsBuilder())->withTargetCredentials('user', 'pass')->build();
        $params = $options->toPostParams();

        $this->assertSame('user', $params['target_username']);
        $this->assertSame('pass', $params['target_password']);
    }

    public function testMultipleOptionsAreMerged(): void
    {
        $options = (new CaptureOptionsBuilder())
            ->withCaptureAll()
            ->withCaptureScreenshot()
            ->withSkipFirstArchive()
            ->withJsBehaviorTimeout(10)
            ->build();

        $params = $options->toPostParams();

        $this->assertSame(1, $params['capture_all']);
        $this->assertSame(1, $params['capture_screenshot']);
        $this->assertSame(1, $params['skip_first_archive']);
        $this->assertSame(10, $params['js_behavior_timeout']);
    }

    public function testBuilderIsImmutable(): void
    {
        $builder = new CaptureOptionsBuilder();
        $builderA = $builder->withCaptureAll();
        $builderB = $builder->withCaptureScreenshot();

        $this->assertNotSame($builder, $builderA);
        $this->assertFalse($builderB->build()->captureAll);
        $this->assertFalse($builderA->build()->captureScreenshot);
    }

    public function testJsBehaviorTimeoutAboveMaxThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CaptureOptions(jsBehaviorTimeout: 31);
    }

    public function testJsBehaviorTimeoutBelowZeroThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CaptureOptions(jsBehaviorTimeout: -1);
    }
}
