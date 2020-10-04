<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\TokenCache;

use DateTimeInterface;

interface ITokenCache
{
    public const DEFAULT_TTL = '+1 hour';
    public const EXPIRE = 'expire';

    public function load(string $cacheKey, ?callable $callback = null): ?string;

    public function save(string $cacheKey, string $token, ?DateTimeInterface $expire = null): void;

    public function clear(string $cacheKey): void;
}
