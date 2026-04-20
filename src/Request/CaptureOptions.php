<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Request;

class CaptureOptions
{
    public function __construct(
        /**
         * Capture a web page with errors (HTTP status=4xx or 5xx). By default, SPN2 captures only status=200 URLs.
         */
        private int $capture_all = 1,
        /**
         * Capture web page outlinks automatically. This also applies to PDF, JSON, RSS and MRSS feeds.
         */
        private int $capture_outlinks = 1,
        /**
         * Capture full page screenshot in PNG format. This is also stored in the Wayback Machine as a different capture.
         */
        private int $capture_screenShot = 1,
        /**
         * The capture becomes available in the Wayback Machine after ~12 hours instead of immediately. This option helps reduce the load on our systems. All API responses remain exactly the same when using this option.
         */
        private int $delay_wb_availability = 1,
        /**
         * Force the use of a simple HTTP GET request to capture the target URL. By default SPN2 does a HTTP HEAD on the target URL to decide whether to use a headless browser or a simple HTTP GET request. force_get overrides this behavior.
         */
        private int $force_get = 1,
        /**
         * Skip checking if a capture is a first if you don’t need this information. This will make captures run faster.
         */
        private int $skip_first_archive = 1,
        /**
         * Capture web page only if the latest existing capture at the Archive is older than the <timedelta> limit.  Its  format could be any datetime expression like “3d 5h 20m” or just a number of seconds, e.g. “120”. If there is a capture within the defined timedelta, SPN2 returns that as a recent capture. The default system <timedelta> is 45 min.
         *
         * When using 2 comma separated <timedelta> values, the first one applies to the main capture and the second one applies to outlinks
         */
        private int|array $if_not_archived_within = 2700,
        /**
         * Return the timestamp of the last capture for all outlinks.
         */
        private int $outlinks_availability = 1,
        /**
         * Send an email report of the captured URLs to the user’s email.
         */
        private int $email_result = 0,
        /*
         * Run JS code for <N> seconds after page load to trigger target page functionality like image loading on mouse over, scroll down to load more content, etc. The default system <N> is 5 sec.
         */
        private int $js_behavior_timeout = 30,
        /**
         * Use extra HTTP Cookie value when capturing the target page.
         */
        private ?string $capture_cookie = null,
        /**
         * Use custom HTTP User-Agent value when capturing the target page.
         */
        private ?string $use_user_agent = null,
        /**
         * Use your own username in the target page’s login forms.
         */
        private ?string $target_username = null,
        /**
         * Use your own password in the target page’s login forms.
         */
        private ?string $target_password = null,
    ) {
    }

    public function getCaptureAll(): int
    {
        return $this->capture_all;
    }

    public function getCaptureOutlinks(): int
    {
        return $this->capture_outlinks;
    }

    public function getCaptureScreenShot(): int
    {
        return $this->capture_screenShot;
    }

    public function getDelayWbAvailability(): int
    {
        return $this->delay_wb_availability;
    }

    public function getForceGet(): int
    {
        return $this->force_get;
    }

    public function getSkipFirstArchive(): int
    {
        return $this->skip_first_archive;
    }

    public function getIfNotArchivedWithin(): int|array
    {
        return $this->if_not_archived_within;
    }

    public function getOutlinksAvailability(): int
    {
        return $this->outlinks_availability;
    }

    public function getEmailResult(): int
    {
        return $this->email_result;
    }

    public function getJsBehaviorTimeout(): int
    {
        return $this->js_behavior_timeout;
    }

    public function getCaptureCookie(): ?string
    {
        return $this->capture_cookie;
    }

    public function getUseUserAgent(): ?string
    {
        return $this->use_user_agent;
    }

    public function getTargetUsername(): ?string
    {
        return $this->target_username;
    }

    public function getTargetPassword(): ?string
    {
        return $this->target_password;
    }
}
