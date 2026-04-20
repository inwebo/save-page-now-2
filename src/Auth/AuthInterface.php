<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Auth;

/**
 * Provides the HTTP headers required to authenticate against the SPN2 API.
 *
 * SPN2 supports two authentication methods:
 *  - S3 API Keys (recommended): Authorization: LOW <access>:<secret>
 *  - Browser cookies: logged-in-user + logged-in-sig
 *
 * @see https://docs.google.com/document/d/1Nsv52MvSjbLb2PCpHlat0gkzw0EvtSgpKHu4mk0MnrA/
 */
interface AuthInterface
{
    /**
     * Returns the HTTP headers that carry the authentication credentials.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array;
}
