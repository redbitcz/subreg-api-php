<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi;

use Redbitcz\SubregApi\Context\Context;
use Redbitcz\SubregApi\TokenCache\FileCache;

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

    /**
     * @see \Redbitcz\SubregApi\Credentials::forAdministrator
     *
     * @param string $user Username for user (company login)
     * @param string $admin Username fro administrator
     * @param string $password
     * @param string|null $tempDir
     * @param string $url
     * @return Context
     */
    public static function createContextForAdministrator(
        string $user,
        string $admin,
        string $password,
        string $tempDir = null,
        string $url = Credentials::DEFAULT_URL
    ): Context {
        return self::createContext("{$admin}#{$user}", $password, $tempDir, $url);
    }
}
