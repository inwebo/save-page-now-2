<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Auth;

/**
 * Cookie-based authentication (fallback method).
 *
 * Retrieve `logged-in-user` and `logged-in-sig` from your browser after
 * logging into https://archive.org. Note that cookies expire after a few
 * days, so S3Credentials are strongly preferred for long-lived integrations.
 */
final class CookieCredentials implements AuthInterface
{
    public function __construct(
        private string $loggedInUser,
        private string $loggedInSig,
    ) {
        if ('' === trim($this->loggedInUser)) {
            throw new \InvalidArgumentException('The logged-in-user cookie must not be empty.');
        }
        if ('' === trim($this->loggedInSig)) {
            throw new \InvalidArgumentException('The logged-in-sig cookie must not be empty.');
        }
    }

    public function getLoggedInUser(): string
    {
        return $this->loggedInUser;
    }

    public function getLoggedInSig(): string
    {
        return $this->loggedInSig;
    }

    public function getHeaders(): array
    {
        return [
            'Cookie' => sprintf(
                'logged-in-sig=%s; logged-in-user=%s',
                $this->loggedInSig,
                $this->loggedInUser,
            ),
        ];
    }
}
