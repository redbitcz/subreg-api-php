<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\TokenCache;

use DateTimeImmutable;
use DateTimeInterface;

class MemoryCache implements ITokenCache
{
    /** @var string[] */
    private $token = [];
    /** @var DateTimeInterface[] */
    private $expire = [];

    public function load(string $cacheKey, ?callable $callback = null): ?string
    {
        if (isset($this->token[$cacheKey]) && $this->expire[$cacheKey] > new DateTimeImmutable()) {
            return $this->token[$cacheKey];
        }

        if ($callback !== null) {
            $deps = [];
            $token = $callback($cacheKey, $deps);
            if (is_string($token)) {
                $expire = ($deps[self::EXPIRE] ?? null) instanceof DateTimeInterface ? $deps[self::EXPIRE] : null;
                $this->save($cacheKey, $token, $expire);
                return $token;
            }
        }

        return null;
    }

    public function save(string $cacheKey, string $token, ?DateTimeInterface $expire = null): void
    {
        $this->expire[$cacheKey] = $expire ?? new DateTimeImmutable(self::DEFAULT_TTL);
        $this->token[$cacheKey] = $token;
    }

    public function clear(string $cacheKey): void
    {
        if (isset($this->token[$cacheKey])) {
            unset($this->token[$cacheKey], $this->expire[$cacheKey]);
        }
    }
}
