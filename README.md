# save-page-now-2

PHP 8.1+ client library for the **Save Page Now 2 (SPN2) API** provided by the [Internet Archive](https://wayback-api.archive.org/save).

## Official Documentation

- [Google Doc (Vangelis Banos, Internet Archive)](https://docs.google.com/document/d/1Nsv52MvSjbLb2PCpHlat0gkzw0EvtSgpKHu4mk0MnrA/)  ← Most comprehensive reference
- [Readable Mirror Gist](https://gist.github.com/regstuff/82e690db2f1d91ba59f6681c1abad6cf)

-----

## Installation

```bash
composer require inwebo/save-page-now-2
```

Obtain your S3 API keys at [https://archive.org/account/s3.php](https://archive.org/account/s3.php).

-----

## Quick Start

```php
use Inwebo\SavePageNow2\Auth\S3Credentials;
use Inwebo\SavePageNow2\Capture\CaptureOptionsBuilder;
use Inwebo\SavePageNow2\Response\JobStatus;
use Inwebo\SavePageNow2\SavePageNow2Client;
use Symfony\Component\HttpClient\HttpClient;

$client = new SavePageNow2Client(
    HttpClient::create(),
    new S3Credentials('my-access-key', 'my-secret'),
);

// 1. Submit a URL
$options = (new CaptureOptionsBuilder())
    ->withSkipFirstArchive()   // Faster
    ->withJsBehaviorTimeout(0) // No JS execution
    ->build();

$job = $client->capture('https://example.com/', $options);
echo "Job started: {$job->jobId}\n";

// 2. Poll until completion
do {
    sleep(5);
    $status = $client->getStatus($job->jobId);
} while ($status->getStatus() === JobStatus::Pending);

// 3. Result
if ($status->getStatus() === JobStatus::Success) {
    echo "✅ Archived: {$status->getWaybackUrl()}\n";
} else {
    echo "❌ Error: {$status->getMessage()} ({$status->getStatusExt()})\n";
}
```

-----

## Architecture

```
src/
├── Auth/
│   ├── AuthInterface.php          Generic authentication interface
│   ├── S3Credentials.php          Authorization: LOW key:secret (recommended)
│   └── CookieCredentials.php      logged-in-user + logged-in-sig (fallback)
│
├── Capture/
│   ├── CaptureOptions.php         Readonly Value object — all POST parameters
│   └── CaptureOptionsBuilder.php  Immutable fluent builder
│
├── Response/
│   ├── JobStatus.php              Enum: Pending | Success | Error
│   ├── CaptureJobResponse.php     POST /save response {url, job_id}
│   ├── UserStatusResponse.php     GET /save/status/user {available, processing}
│   ├── SystemStatusResponse.php   GET /save/status/system {status}
│   └── Status/
│       ├── StatusResponseInterface.php
│       ├── StatusResponseFactory.php   Dispatches JSON → correct implementation
│       ├── PendingStatusResponse.php
│       ├── SuccessStatusResponse.php   + getWaybackUrl()
│       └── ErrorStatusResponse.php     + getStatusExt(), getMessage()
│
├── Exception/
│   ├── SavePageNowException.php       Base exception
│   ├── ApiException.php               Unexpected / malformed response
│   ├── AuthenticationException.php    HTTP 401 / error:unauthorized
│   ├── UserSessionLimitException.php  error:user-session-limit
│   └── NetworkException.php           Symfony transport error
│
├── SavePageNow2Interface.php      Public client contract
└── SavePageNow2Client.php         Symfony HttpClient implementation
```

-----

## Complete API

### `capture(string $url, ?CaptureOptions $options = null): CaptureJobResponse`

Submits a URL for archiving. Returns a `job_id` immediately.

### `getStatus(string $jobId): StatusResponseInterface`

Returns a `PendingStatusResponse`, `SuccessStatusResponse`, or `ErrorStatusResponse`.

### `getStatuses(array $jobIds): array<string, StatusResponseInterface>`

Retrieves the status of multiple jobs in a single request.

### `getOutlinksStatus(string $parentJobId): array<string, StatusResponseInterface>`

Retrieves the status of all outlinks for a parent job (requires `capture_outlinks=1`).

### `getUserStatus(): UserStatusResponse`

Active and available sessions for the authenticated user.

### `getSystemStatus(): SystemStatusResponse`

Overall health of the SPN2 service.

-----

## Capture Options

| Builder method                          | API Parameter             | Description |
|-----------------------------------------|---------------------------|-------------|
| `withCaptureAll()`                      | `capture_all=1`           | Also captures 4xx/5xx pages |
| `withCaptureOutlinks()`                 | `capture_outlinks=1`      | Automatically archives outlinks |
| `withCaptureScreenshot()`               | `capture_screenshot=1`    | Captures a full-page PNG screenshot |
| `withDelayWbAvailability()`             | `delay_wb_availability=1` | Available in \~12h (reduces server load) |
| `withForceGet()`                        | `force_get=1`             | Forces a simple GET (no headless browser) |
| `withSkipFirstArchive()`                | `skip_first_archive=1`    | Skips the "first archive" check (faster) |
| `withIfNotArchivedWithin(string)`       | `if_not_archived_within`  | Only archives if older than e.g., `"3d 5h"` |
| `withOutlinksAvailability()`            | `outlinks_availability=1` | Returns the last snapshot timestamp for each outlink |
| `withEmailResult()`                     | `email_result=1`          | Sends an email report |
| `withJsBehaviorTimeout(int $s)`         | `js_behavior_timeout=N`   | JS execution time after loading (0–30s) |
| `withCaptureCookie(string)`             | `capture_cookie`          | Additional HTTP cookie for the target |
| `withTargetCredentials(string, string)` | `target_username/password`| Credentials for the target's auth forms |

-----

## Error Handling

```php
use Inwebo\SavePageNow2\Exception\AuthenticationException;
use Inwebo\SavePageNow2\Exception\UserSessionLimitException;
use Inwebo\SavePageNow2\Exception\NetworkException;
use Inwebo\SavePageNow2\Exception\ApiException;

try {
    $job = $client->capture('https://example.com/');
} catch (AuthenticationException $e) {
    // Invalid or expired S3 keys
} catch (UserSessionLimitException $e) {
    // 12 simultaneous captures reached (auth) / 6 (anonymous)
} catch (NetworkException $e) {
    // Transport issue (timeout, DNS...)
} catch (ApiException $e) {
    // Unexpected API response
}
```

Detailed error codes from `status_ext` (e.g., `error:not-found`, `error:too-many-daily-captures`) are available via `ErrorStatusResponse::getStatusExt()`.

-----

## Testing

Run tests using the included PHPUnit runner:

```bash
composer phpunit
```

```
Save Page Now 2 — Test Suite
========================================
..................................................... 53 / 53
OK (53 tests, 97 assertions)
```

-----

## License

MIT