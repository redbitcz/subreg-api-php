<?php

declare(strict_types=1);

namespace Redbitcz\SubregApiTests;

use Redbitcz\SubregApi\Factory;
use Redbitcz\SubregApi\TokenCache\FileCache;
use Redbitcz\SubregApi\TokenCache\MemoryCache;
use Tester\Assert;
use Tester\Helpers;

require __DIR__ . '/../vendor/autoload.php';

/**
 * @testCase
 */
class FactoryTest extends \Tester\TestCase
{
    public function testCreateClientSimple(): void
    {
        $client = Factory::createClient('microsoft', 'password');

        Assert::with(
            $client,
            function () {
                /** @noinspection PhpUndefinedFieldInspection */
                $credentials = $this->credentials;
                /** @noinspection PhpUndefinedFieldInspection */
                $tokenCache = $this->tokenCache;
                Assert::equal('microsoft', $credentials->getLogin());
                Assert::equal('password', $credentials->getPassword());
                Assert::type(MemoryCache::class, $tokenCache);
            }
        );
    }

    public function testCreateClientCache(): void
    {
        Helpers::purge(__DIR__ . '/../temp');
        $client = Factory::createClient('microsoft', 'password', __DIR__ . '/../temp');

        Assert::with(
            $client,
            function () {
                /** @noinspection PhpUndefinedFieldInspection */
                $tokenCache = $this->tokenCache;
                Assert::type(FileCache::class, $tokenCache);
            }
        );
    }

    public function testCreateClientUrl(): void
    {
        $client = Factory::createClient('microsoft', 'password', null, 'url://hello');

        Assert::with(
            $client,
            function () {
                /** @noinspection PhpUndefinedFieldInspection */
                $credentials = $this->credentials;
                Assert::equal('url://hello', $credentials->getUrl());
            }
        );
    }

    public function testCreateClientAdministrator(): void
    {
        $client = Factory::createClientForAdministrator('microsoft', 'gates', 'password');

        Assert::with(
            $client,
            function () {
                /** @noinspection PhpUndefinedFieldInspection */
                $credentials = $this->credentials;
                /** @noinspection PhpUndefinedFieldInspection */
                $tokenCache = $this->tokenCache;
                Assert::equal('gates#microsoft', $credentials->getLogin());
                Assert::equal('password', $credentials->getPassword());
                Assert::type(MemoryCache::class, $tokenCache);
            }
        );
    }

    public function testCreateClientCacheAdministrator(): void
    {
        Helpers::purge(__DIR__ . '/../temp');
        $client = Factory::createClientForAdministrator('microsoft', 'gates', 'password', __DIR__ . '/../temp');

        Assert::with(
            $client,
            function () {
                /** @noinspection PhpUndefinedFieldInspection */
                $tokenCache = $this->tokenCache;
                Assert::type(FileCache::class, $tokenCache);
            }
        );
    }

    public function testCreateClientUrlAdministrator(): void
    {
        $client = Factory::createClientForAdministrator('microsoft', 'gates', 'password', null, 'url://hello');

        Assert::with(
            $client,
            function () {
                /** @noinspection PhpUndefinedFieldInspection */
                $credentials = $this->credentials;
                Assert::equal('url://hello', $credentials->getUrl());
            }
        );
    }
}

(new FactoryTest())->run();
