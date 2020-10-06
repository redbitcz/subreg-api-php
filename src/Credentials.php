<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi;

class Credentials
{
    public const DEFAULT_URL = 'https://soap.subreg.cz/cmd.php';
    public const DEFAULT_NAMESPACE = 'https://soap.subreg.cz/soap';

    /** @var string */
    private $login;

    /** @var string */
    private $password;

    /** @var string */
    private $url;

    /** @var string */
    private $namespace;

    public function __construct(
        string $login,
        string $password,
        string $url = self::DEFAULT_URL,
        string $namespace = self::DEFAULT_NAMESPACE
    ) {
        $this->password = $password;
        $this->login = $login;
        $this->url = $url;
        $this->namespace = $namespace;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getIdentityHash(): string
    {
        return md5(serialize([$this->login, $this->password, $this->url, $this->namespace]));
    }

    /**
     * Create Credentials for administrator account
     * You can you your admin account in format `admin#user` (for example `gates#microsoft`) to login!
     * You must use admin password instead of your account password and admin must have API access sign.
     *
     * @param string $user Username for user (company login)
     * @param string $admin Username fro administrator
     * @param string $password
     * @param string $url
     * @param string $namespace
     * @return self
     */
    public static function forAdministrator(
        string $user,
        string $admin,
        string $password,
        string $url = self::DEFAULT_URL,
        string $namespace = self::DEFAULT_NAMESPACE
    ): self {
        return new self(self::mergeAdminToLogin($user, $admin), $password, $url, $namespace);
    }

    public static function mergeAdminToLogin(string $user, string $admin): string
    {
        return "{$admin}#{$user}";
    }
}
