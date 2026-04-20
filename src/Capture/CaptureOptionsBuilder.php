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

    /**
     * Capture a web page with errors (HTTP status=4xx or 5xx). By default, SPN2 captures only status=200 URLs.
     */
    public function withCaptureAll(bool $value = true): self
    {
        $clone = clone $this;
        $clone->captureAll = $value;

        return $clone;
    }
    /**
     * Capture web page outlinks automatically. This also applies to PDF, JSON, RSS, and MRSS feeds.
     */
    public function withCaptureOutlinks(bool $value = true): self
    {
        $clone = clone $this;
        $clone->captureOutlinks = $value;

        return $clone;
    }
    /**
     * Capture full page screenshot in PNG format. This is also stored in the Wayback Machine as a different capture.
     */
    public function withCaptureScreenshot(bool $value = true): self
    {
        $clone = clone $this;
        $clone->captureScreenshot = $value;

        return $clone;
    }
    /**
     * The capture becomes available in the Wayback Machine after ~12 hours instead of immediately. This option helps reduce the load on our systems. All API responses remain exactly the same when using this option.
     */
    public function withDelayWbAvailability(bool $value = true): self
    {
        $clone = clone $this;
        $clone->delayWbAvailability = $value;

        return $clone;
    }
    /**
     * Force the use of a simple HTTP GET request to capture the target URL. By default, SPN2 does an HTTP HEAD on the target URL to decide whether to use a headless browser or a simple HTTP GET request. Option force_get overrides this behavior.
     */
    public function withForceGet(bool $value = true): self
    {
        $clone = clone $this;
        $clone->forceGet = $value;

        return $clone;
    }
    /**
     * Skip checking if a capture is a first if you don’t need this information. This will make captures run faster.
     */
    public function withSkipFirstArchive(bool $value = true): self
    {
        $clone = clone $this;
        $clone->skipFirstArchive = $value;

        return $clone;
    }
    /**
     * Capture web page only if the latest existing capture at the Archive is older than the <timedelta> limit.  Its format could be any datetime expression like “3d 5h 20m” or just a number of seconds, e.g. “120”. If there is a capture within the defined timedelta, SPN2 returns that as a recent capture. The default system <timedelta> is 45 min.
     *
     * When using 2 comma-separated <timedelta> values, the first one applies to the main capture and the second one applies to outlinks
     */
    public function withIfNotArchivedWithin(string $timeDelta): self
    {
        $clone = clone $this;
        $clone->ifNotArchivedWithin = $timeDelta;

        return $clone;
    }
    /**
     * Return the timestamp of the last capture for all outlinks.
     */
    public function withOutlinksAvailability(bool $value = true): self
    {
        $clone = clone $this;
        $clone->outlinksAvailability = $value;

        return $clone;
    }
    /**
     * Send an email report of the captured URLs to the user’s email.
     */
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
    /**
     * Use extra HTTP Cookie value when capturing the target page.
     */
    public function withCaptureCookie(string $cookie): self
    {
        $clone = clone $this;
        $clone->captureCookie = $cookie;

        return $clone;
    }
    /**
     * Use your own username in the target page’s login forms.
     * Use your own password in the target page’s login forms.
     */
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
