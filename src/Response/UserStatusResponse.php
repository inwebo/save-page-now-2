<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Response;

/**
 * Response from GET /save/status/user.
 *
 * Example: {"available":12,"processing":3}
 *
 * Authenticated users may run up to 12 concurrent captures.
 */
final class UserStatusResponse
{
    public function __construct(
        public readonly int $available,
        public readonly int $processing,
    ) {
    }

    /**
     * @param array<string, ?int> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            available: ($data['available'] ?? 0),
            processing: ($data['processing'] ?? 0),
        );
    }

    /**
     * Returns true when the user still has capture slots available.
     */
    public function hasAvailableSlots(): bool
    {
        return $this->available > 0;
    }
}
