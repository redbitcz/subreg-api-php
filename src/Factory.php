<?php

declare(strict_types=1);

namespace Soukicz\SubregApi;

use Soukicz\SubregApi\Context\Context;
use Soukicz\SubregApi\TokenCache\FileCache;

class Factory
{
    public static function createContext(
        string $login,
        string $password,
        string $tempDir = null,
        string $url = Credentials::DEFAULT_URL
    ): Context {
        $cache = $tempDir ? new FileCache($tempDir) : null;
        $client = new Client(new Credentials($login, $password, $url), $cache);
        return new Context($client);
    }
}
