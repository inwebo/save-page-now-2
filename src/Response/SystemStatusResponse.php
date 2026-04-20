<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Response;

/**
 * Response from GET /save/status/system.
 *
 * When healthy : {"status":"ok"}
 * When overloaded: {"status":"Save Page Now servers are temporarily overloaded. Your captures may be delayed."}
 * Critical failure: HTTP 502 (handled as an exception upstream)
 */
final class SystemStatusResponse
{
    private const STATUS_OK = 'ok';

    public function __construct(
        public readonly string $status,
    ) {
    }

    /**
     * @param array<string, ?string> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(status: ($data['status'] ?? ''));
    }

    public function isOk(): bool
    {
        return self::STATUS_OK === $this->status;
    }

    public function isOverloaded(): bool
    {
        return !$this->isOk();
    }
}
