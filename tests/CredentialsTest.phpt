<?php

declare(strict_types=1);

/**
 * Test: \Redbitcz\SubregApi\Helpers tests
 */

use Redbitcz\SubregApi\Credentials;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

test(
    static function(){
    $credentials = new Credentials('microsoft', 'password');

    Assert::equal('microsoft', $credentials->getLogin());
    Assert::equal('password', $credentials->getPassword());
    Assert::equal(Credentials::DEFAULT_URL, $credentials->getUrl());
    Assert::equal(Credentials::DEFAULT_NAMESPACE, $credentials->getNamespace());
});

test(
    static function(){
    $credentials = new Credentials('microsoft', 'password', 'url://hello', 'foo://bar');

    Assert::equal('microsoft', $credentials->getLogin());
    Assert::equal('password', $credentials->getPassword());
    Assert::equal('url://hello', $credentials->getUrl());
    Assert::equal('foo://bar', $credentials->getNamespace());
});

test(
    static function(){
    $credentials = Credentials::forAdministrator('microsoft', 'gates', 'password');

    Assert::equal('gates#microsoft', $credentials->getLogin());
    Assert::equal('password', $credentials->getPassword());
    Assert::equal(Credentials::DEFAULT_URL, $credentials->getUrl());
    Assert::equal(Credentials::DEFAULT_NAMESPACE, $credentials->getNamespace());
});

test(
    static function(){
    $credentials = Credentials::forAdministrator('microsoft', 'gates', 'password', 'url://hello', 'foo://bar');

    Assert::equal('gates#microsoft', $credentials->getLogin());
    Assert::equal('password', $credentials->getPassword());
    Assert::equal('url://hello', $credentials->getUrl());
    Assert::equal('foo://bar', $credentials->getNamespace());
});

test(
    static function(){
        $login = Credentials::mergeAdminToLogin('microsoft', 'gates');

        Assert::equal('gates#microsoft', $login);
    });
