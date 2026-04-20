<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Capture;

/**
 * Fluent builder for CaptureOptions.
 *
 * Example:
 *   $options = (new CaptureOptionsBuilder())
 *       ->withCaptureOutlinks()
 *       ->withSkipFirstArchive()
 *       ->withJsBehaviorTimeout(0)
 *       ->build();
 */
final class CaptureOptionsBuilder
{
    private bool $captureAll = false;
    private bool $captureOutlinks = false;
    private bool $captureScreenshot = false;
    private bool $delayWbAvailability = false;
    private bool $forceGet = false;
    private bool $skipFirstArchive = false;
    private ?string $ifNotArchivedWithin = null;
    private bool $outlinksAvailability = false;
    private bool $emailResult = false;
    private ?int $jsBehaviorTimeout = null;
    private ?string $captureCookie = null;
    private ?string $targetUsername = null;
    private ?string $targetPassword = null;

    public function withCaptureAll(bool $value = true): self
    {
        $clone = clone $this;
        $clone->captureAll = $value;

        return $clone;
    }

    public function withCaptureOutlinks(bool $value = true): self
    {
        $clone = clone $this;
        $clone->captureOutlinks = $value;

        return $clone;
    }

    public function withCaptureScreenshot(bool $value = true): self
    {
        $clone = clone $this;
        $clone->captureScreenshot = $value;

        return $clone;
    }

    public function withDelayWbAvailability(bool $value = true): self
    {
        $clone = clone $this;
        $clone->delayWbAvailability = $value;

        return $clone;
    }

    public function withForceGet(bool $value = true): self
    {
        $clone = clone $this;
        $clone->forceGet = $value;

        return $clone;
    }

    public function withSkipFirstArchive(bool $value = true): self
    {
        $clone = clone $this;
        $clone->skipFirstArchive = $value;

        return $clone;
    }

    public function withIfNotArchivedWithin(string $timedelta): self
    {
        $clone = clone $this;
        $clone->ifNotArchivedWithin = $timedelta;

        return $clone;
    }

    public function withOutlinksAvailability(bool $value = true): self
    {
        $clone = clone $this;
        $clone->outlinksAvailability = $value;

        return $clone;
    }

    public function withEmailResult(bool $value = true): self
    {
        $clone = clone $this;
        $clone->emailResult = $value;

        return $clone;
    }

    /**
     * @param int $seconds 0–30 seconds. Use 0 for pages with no JS to speed up capture.
     */
    public function withJsBehaviorTimeout(int $seconds): self
    {
        $clone = clone $this;
        $clone->jsBehaviorTimeout = $seconds;

        return $clone;
    }

    public function withCaptureCookie(string $cookie): self
    {
        $clone = clone $this;
        $clone->captureCookie = $cookie;

        return $clone;
    }

    public function withTargetCredentials(string $username, string $password): self
    {
        $clone = clone $this;
        $clone->targetUsername = $username;
        $clone->targetPassword = $password;

        return $clone;
    }

    public function build(): CaptureOptions
    {
        return new CaptureOptions(
            captureAll: $this->captureAll,
            captureOutlinks: $this->captureOutlinks,
            captureScreenshot: $this->captureScreenshot,
            delayWbAvailability: $this->delayWbAvailability,
            forceGet: $this->forceGet,
            skipFirstArchive: $this->skipFirstArchive,
            ifNotArchivedWithin: $this->ifNotArchivedWithin,
            outlinksAvailability: $this->outlinksAvailability,
            emailResult: $this->emailResult,
            jsBehaviorTimeout: $this->jsBehaviorTimeout,
            captureCookie: $this->captureCookie,
            targetUsername: $this->targetUsername,
            targetPassword: $this->targetPassword,
        );
    }
}
