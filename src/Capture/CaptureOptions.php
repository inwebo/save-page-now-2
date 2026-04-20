<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Capture;

/**
 * Immutable value object that holds all optional parameters for a capture request.
 *
 * Use CaptureOptionsBuilder to construct instances fluently.
 *
 * @see https://docs.google.com/document/d/1Nsv52MvSjbLb2PCpHlat0gkzw0EvtSgpKHu4mk0MnrA/
 */
final class CaptureOptions
{
    /**
     * @param bool        $captureAll           capture pages with HTTP 4xx/5xx errors
     * @param bool        $captureOutlinks      automatically capture outlinks
     * @param bool        $captureScreenshot    capture a full-page PNG screenshot
     * @param bool        $delayWbAvailability  make the capture available in ~12 h instead of immediately
     * @param bool        $forceGet             force a plain HTTP GET (skip HEAD-based browser decision)
     * @param bool        $skipFirstArchive     skip the "first archive" check for faster captures
     * @param string|null $ifNotArchivedWithin  only capture if the last snapshot is older than this timedelta
     * @param bool        $outlinksAvailability return the last-capture timestamp for each outlink
     * @param bool        $emailResult          send an email report to the account email
     * @param int|null    $jsBehaviorTimeout    seconds to run JS after page load (0 to disable, max 30)
     * @param string|null $captureCookie        extra Cookie header sent when fetching the target URL
     * @param string|null $targetUsername       username for target-page login forms
     * @param string|null $targetPassword       password for target-page login forms
     */
    public function __construct(
        public readonly bool $captureAll = false,
        public readonly bool $captureOutlinks = false,
        public readonly bool $captureScreenshot = false,
        public readonly bool $delayWbAvailability = false,
        public readonly bool $forceGet = false,
        public readonly bool $skipFirstArchive = false,
        public readonly ?string $ifNotArchivedWithin = null,
        public readonly bool $outlinksAvailability = false,
        public readonly bool $emailResult = false,
        public readonly ?int $jsBehaviorTimeout = null,
        public readonly ?string $captureCookie = null,
        public readonly ?string $targetUsername = null,
        public readonly ?string $targetPassword = null,
    ) {
        if (null !== $this->jsBehaviorTimeout && ($this->jsBehaviorTimeout < 0 || $this->jsBehaviorTimeout > 30)) {
            throw new \InvalidArgumentException(sprintf('jsBehaviorTimeout must be between 0 and 30, got %d.', $this->jsBehaviorTimeout));
        }
    }

    /**
     * Serialises options to the POST body array expected by the SPN2 API.
     *
     * @return array<string, string|int>
     */
    public function toPostParams(): array
    {
        $params = [];

        if ($this->captureAll) {
            $params['capture_all'] = 1;
        }
        if ($this->captureOutlinks) {
            $params['capture_outlinks'] = 1;
        }
        if ($this->captureScreenshot) {
            $params['capture_screenshot'] = 1;
        }
        if ($this->delayWbAvailability) {
            $params['delay_wb_availability'] = 1;
        }
        if ($this->forceGet) {
            $params['force_get'] = 1;
        }
        if ($this->skipFirstArchive) {
            $params['skip_first_archive'] = 1;
        }
        if (null !== $this->ifNotArchivedWithin) {
            $params['if_not_archived_within'] = $this->ifNotArchivedWithin;
        }
        if ($this->outlinksAvailability) {
            $params['outlinks_availability'] = 1;
        }
        if ($this->emailResult) {
            $params['email_result'] = 1;
        }
        if (null !== $this->jsBehaviorTimeout) {
            $params['js_behavior_timeout'] = $this->jsBehaviorTimeout;
        }
        if (null !== $this->captureCookie) {
            $params['capture_cookie'] = $this->captureCookie;
        }
        if (null !== $this->targetUsername) {
            $params['target_username'] = $this->targetUsername;
        }
        if (null !== $this->targetPassword) {
            $params['target_password'] = $this->targetPassword;
        }

        return $params;
    }
}
