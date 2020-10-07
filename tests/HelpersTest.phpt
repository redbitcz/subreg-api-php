<?php

declare(strict_types=1);

/**
 * Test: \Redbitcz\SubregApi\Helpers tests
 */

use Redbitcz\SubregApi\Helpers;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

test(
    static function () {
        $class = new stdClass();
        $date = new DateTime();

        Assert::equal(0, Helpers::soapInt(0));
        Assert::equal(0.1, Helpers::soapInt(0.1));
        Assert::equal(0, Helpers::soapInt('0'));
        Assert::equal(1234567890, Helpers::soapInt('1234567890'));
        Assert::equal(null, Helpers::soapInt(null));
        Assert::equal('0.0', Helpers::soapInt('0.0'));
        Assert::equal('0e0', Helpers::soapInt('0e0'));
        Assert::equal('hello', Helpers::soapInt('hello'));
        Assert::equal(true, Helpers::soapInt(true));
        Assert::equal(false, Helpers::soapInt(false));
        Assert::equal([], Helpers::soapInt([]));
        Assert::equal(['a'], Helpers::soapInt(['a']));
        Assert::equal(['a' => 'b'], Helpers::soapInt(['a' => 'b']));
        Assert::equal($class, Helpers::soapInt($class));
        Assert::equal($date, Helpers::soapInt($date));
    }
);


test(
    static function () {
        $class = new stdClass();
        $date = new DateTime();

        Assert::equal(0, Helpers::soapFloat(0));
        Assert::equal(0.1, Helpers::soapFloat(0.1));
        Assert::equal(0.0, Helpers::soapFloat('0'));
        Assert::equal(1234567890.0, Helpers::soapFloat('1234567890'));
        Assert::equal(null, Helpers::soapFloat(null));
        Assert::equal(0.0, Helpers::soapFloat('0.0'));
        Assert::equal(0.1, Helpers::soapFloat('0.1'));
        Assert::equal('0e0', Helpers::soapFloat('0e0'));
        Assert::equal('hello', Helpers::soapFloat('hello'));
        Assert::equal(true, Helpers::soapFloat(true));
        Assert::equal(false, Helpers::soapFloat(false));
        Assert::equal([], Helpers::soapFloat([]));
        Assert::equal(['a'], Helpers::soapFloat(['a']));
        Assert::equal(['a' => 'b'], Helpers::soapFloat(['a' => 'b']));
        Assert::equal($class, Helpers::soapFloat($class));
        Assert::equal($date, Helpers::soapFloat($date));
    }
);

test(
    static function () {
        $class = new stdClass();

        $class2 = new stdClass();
        $class2->foo = 'bar';

        $class3 = new stdClass();
        $class3->boo = clone $class2;
        $class3->boo->moo = clone $class;

        $date = new DateTime();
        $exception = new LogicException('Test', 1234);

        Assert::equal([0], Helpers::toArray(0));
        Assert::equal([0.1], Helpers::toArray(0.1));
        Assert::equal(['0'], Helpers::toArray('0'));
        Assert::equal(['1234567890'], Helpers::toArray('1234567890'));
        Assert::equal([], Helpers::toArray(null));
        Assert::equal(['0.0'], Helpers::toArray('0.0'));
        Assert::equal(['0.1'], Helpers::toArray('0.1'));
        Assert::equal(['0e0'], Helpers::toArray('0e0'));
        Assert::equal(['hello'], Helpers::toArray('hello'));
        Assert::equal([true], Helpers::toArray(true));
        Assert::equal([false], Helpers::toArray(false));
        Assert::equal([], Helpers::toArray([]));
        Assert::equal(['a'], Helpers::toArray(['a']));
        Assert::equal(['a' => 'b'], Helpers::toArray(['a' => 'b']));
        Assert::equal(['a' => 'b', 'b' => []], Helpers::toArray(['a' => 'b', 'b' => $class]));
        Assert::equal((array)$date, Helpers::toArray($date));
        Assert::equal((array)$exception, Helpers::toArray($exception));
        Assert::equal([], Helpers::toArray($class));
        Assert::equal(['foo' => 'bar'], Helpers::toArray($class2));
        Assert::equal(['boo' => ['foo' => 'bar', 'moo' => []]], Helpers::toArray($class3));
    }
);