<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi;

use Redbitcz\SubregApi\Context\Context;
use Redbitcz\SubregApi\TokenCache\FileCache;

class Factory
{
    public static function createClient(
        string $login,
        string $password,
        string $tempDir = null,
        string $url = Credentials::DEFAULT_URL
    ): Client {
        $cache = $tempDir ? new FileCache($tempDir) : null;
        return new Client(new Credentials($login, $password, $url), $cache);
    }

    /**
     * @param string $user Username for user (company login)
     * @param string $admin Username fro administrator
     * @param string $password
     * @param string|null $tempDir
     * @param string $url
     * @return Client
     * @see \Redbitcz\SubregApi\Credentials::forAdministrator
     *
     */
    public static function createClientForAdministrator(
        string $user,
        string $admin,
        string $password,
        string $tempDir = null,
        string $url = Credentials::DEFAULT_URL
    ): Client {
        return self::createClient("{$admin}#{$user}", $password, $tempDir, $url);
    }

    public static function createContext(
        string $login,
        string $password,
        string $tempDir = null,
        string $url = Credentials::DEFAULT_URL
    ): Context {
        $client = self::createClient($login, $password, $tempDir, $url);
        return new Context($client);
    }

    /**
     * @param string $user Username for user (company login)
     * @param string $admin Username fro administrator
     * @param string $password
     * @param string|null $tempDir
     * @param string $url
     * @return Context
     * @see \Redbitcz\SubregApi\Credentials::forAdministrator
     *
     */
    public static function createContextForAdministrator(
        string $user,
        string $admin,
        string $password,
        string $tempDir = null,
        string $url = Credentials::DEFAULT_URL
    ): Context {
        $client = self::createClientForAdministrator($user, $admin, $password, $tempDir, $url);
        return new Context($client);
    }
}
