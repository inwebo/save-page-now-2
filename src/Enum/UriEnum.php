<?php

declare(strict_types=1);

namespace Inwebo\SavePageNow2\Enum;

enum UriEnum: string
{
    public const CAPTURE = 'https://web.archive.org/save';
    public const CAPTURE_STATUS = 'https://web.archive.org/save/status/';
    public const CAPTURE_CREATED = 'https://web.archive.org/web/';
    public const USER_STATUS = 'https://web.archive.org/save/status/user';
    public const SYSTEM_STATUS = 'https://web.archive.org/save/status/system';
}
