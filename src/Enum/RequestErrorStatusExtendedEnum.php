<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Enum;

enum RequestErrorStatusExtendedEnum: string
{
    /**
     * Bad Gateway for URL (HTTP status=502).
     */
    case BAD_GATEWAY = 'error:bad-gateway';
    /**
     * The server could not understand the request due to invalid syntax. (HTTP status=401).
     */
    case BAD_REQUEST = 'error:bad-request';
    /**
     * The target server has exceeded the bandwidth specified by the server administrator. (HTTP status=509).
     */
    case BANDWIDTH_LIMIT_EXCEEDED = 'error:bandwidth-limit-exceeded';
    /**
     * The target site is blocking us (HTTP status=999).
     */
    case BLOCKED = 'error:blocked';
    /**
     * Anonymous clients which are listed in https://www.spamhaus.org/xbl/ or https://www.spamhaus.org/sbl/ lists (spams & exploits) are blocked.
     * Tor exit nodes are excluded from this filter.
     */
    case BLOCKED_CLIENT_IP = 'error:blocked-client-ip';
    /**
     * We use a URL blocklist based on Mozilla web tracker lists to avoid unwanted captures.
     */
    case BLOCKED_URL = 'error:blocked-url';
    /**
     * SPN2 back-end headless browser timeout.
     */
    case BROWSING_TIMEOUT = 'error:browsing-timeout';
    /**
     * SPN2 back-end cannot find the created capture location. (system error).
     */
    case CAPTURE_LOCATION_ERROR = 'error:capture-location-error';
    /**
     * Cannot fetch the target URL due to system overload.
     */
    case CANNOT_FETCH = 'error:cannot-fetch';
    /**
     * Cannot start capture task.
     */
    case CELERY = 'error:celery';
    /**
     * Cannot capture web resources over 2GB.
     */
    case FILESIZE_LIMIT = 'error:filesize-limit';
    /**
     * Tried to capture an FTP resource but access was denied.
     */
    case FTP_ACCESS_DENIED = 'error:ftp-access-denied';
    /**
     * The target server didn't respond in time. (HTTP status=504).
     */
    case GATEWAY_TIMEOUT = 'error:gateway-timeout';
    /**
     * The target server does not support the HTTP protocol version used in the request for URL (HTTP status=505).
     */
    case HTTP_VERSION_NOT_SUPPORTED = 'error:http-version-not-supported';
    /**
     * SPN internal server error.
     */
    case INTERNAL_SERVER_ERROR = 'error:internal-server-error';
    /**
     * Target URL syntax is not valid.
     */
    case INVALID_URL_SYNTAX = 'error:invalid-url-syntax';
    /**
     * The target server response was invalid. (e.g. invalid headers, invalid content encoding, etc).
     */
    case INVALID_SERVER_RESPONSE = 'error:invalid-server-response';
    /**
     * Couldn't resolve the target host.
     */
    case INVALID_HOST_RESOLUTION = 'error:invalid-host-resolution';
    /**
     * Capture failed due to system error.
     */
    case JOB_FAILED = 'error:job-failed';
    /**
     * The request method is known by the server but has been disabled and cannot be used (HTTP status=405).
     */
    case METHOD_NOT_ALLOWED = 'error:method-not-allowed';
    /**
     * The request method is not supported by the server and cannot be handled (HTTP status=501).
     */
    case NOT_IMPLEMENTED = 'error:not-implemented';
    /**
     * SPN2 back-end headless browser cannot run.
     */
    case NO_BROWSERS_AVAILABLE = 'error:no-browsers-available';
    /**
     * The client needs to authenticate to gain network access to the URL (HTTP status=511).
     */
    case NETWORK_AUTHENTICATION_REQUIRED = 'error:network-authentication-required';
    /**
     * Target URL could not be accessed (status=403).
     */
    case NO_ACCESS = 'error:no-access';
    /**
     * Target URL not found (status=404).
     */
    case NOT_FOUND = 'error:not-found';
    /**
     * SPN2 back-end proxy error.
     */
    case PROXY_ERROR = 'error:proxy-error';
    /**
     * HTTP connection broken. (A possible cause of this error is "IncompleteRead").
     */
    case PROTOCOL_ERROR = 'error:protocol-error';
    /**
     * HTTP connection read timeout.
     */
    case READ_TIMEOUT = 'error:read-timeout';
    /**
     * Capture duration exceeded 45s time limit and was terminated.
     */
    case SOFT_TIME_LIMIT_EXCEEDED = 'error:soft-time-limit-exceeded';
    /**
     * Service unavailable for URL (HTTP status=503).
     */
    case SERVICE_UNAVAILABLE = 'error:service-unavailable';
    /**
     * This URL has been captured 10 times today. We cannot make any more captures.
     */
    case TOO_MANY_DAILY_CAPTURES = 'error:too-many-daily-captures';
    /**
     * Too many redirects. SPN2 tries to follow 3 redirects automatically.
     */
    case TOO_MANY_REDIRECTS = 'error:too-many-redirects';
    /**
     * The target host has received too many requests from SPN and it is blocking it. (HTTP status=429).
     * Note that captures to the same host will be delayed for 10-20s after receiving this response to remedy the situation.
     */
    case TOO_MANY_REQUESTS = 'error:too-many-requests';
    /**
     * User has reached the limit of concurrent active capture sessions.
     */
    case USER_SESSION_LIMIT = 'error:user-session-limit';
    /**
     * The server requires authentication (HTTP status=401).
     */
    case UNAUTHORIZED = 'error:unauthorized';
    /**
     * An authenticated user can archive up to 5GB per day.
     */
    case MAX_DAILY_BANDWIDTH = 'error:max-daily-bandwidth';
    /**
     * An anonymous user can archive up to 2GB per day.
     */
    case MAX_DAILY_BANDWIDTH_FROM_IP = 'error:max-daily-bandwidth-from-ip';
    /**
     * SPN2 can archive up to 100GB per day from a host.
     */
    case MAX_DAILY_BANDWIDTH_HOST = 'error:max-daily-bandwidth-host';
}
