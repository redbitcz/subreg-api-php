# Subreg.cz API client for PHP

Package for simple and safe access to [**Subreg.cz API**](https://subreg.cz/manual/).

## About Subreg API
- [**Subreg.cz API Documentation**](https://subreg.cz/manual/)
- [**Subreg.cz API WDSL Specification**](https://subreg.cz/wsdl)

## Installation

Install via Composer:

```shell
composer install redbitcz/subreg-api-php
```

It requires PHP version 7.3 and supports PHP up to 7.4.

## Access level

You can use Package with two ways:
- **raw access**: just call raw command and get raw response – the Package only handle Request authentication,
    Connection or Server errors and check basic Envelope od Response data. 
- **context access**: full service access which is allow traversing over data, checks Request and Response data by
    strict Schema, access to all properties via strict-typed Getters.

For more information see documentation bellow.

## Authentication & Environment
### Authentication

For authentication to API use `Credentials` class:

```php
$credentials = new \Redbitcz\SubregApi\Credentials('microsoft', 'password');
```

If you are using [Subreg administrator access](https://subreg.cz/en/settings/admins/) to account, use '`#`' to join
 username and administrator name:

```php
$credentials = new \Redbitcz\SubregApi\Credentials('gates#microsoft', 'password');
``` 

For more information about [administrator access](https://subreg.cz/en/settings/admins/) visit
[Subreg API Login documentation page](https://subreg.cz/manual/?cmd=Login).

### Live / Test Environment

Package is process all requests to Production Environment.

If you need try your App to [test Environment](https://subreg.cz/manual/?cmd=Main), you can change URL Endpoint:

```php
$credentials = new \Redbitcz\SubregApi\Credentials('microsoft', 'password', 'https://ote-soap.subreg.cz/cmd.php');
```  

## Usage Raw access

Use `Credentials` object to create `Client`.

```php
$client = new \Redbitcz\SubregApi\Client($credentials);
```  

Client has only method: `$client->call()` to send authenticated request to Subreg.cz API.

```php
$client->call('Check_Domain', ['domain' => 'subreg.cz'])->getData();

/*
    Returns the Array:

    [
      'name' => 'subreg.cz',
      'avail' => 0,
      'price' => 
      [
        'amount' => 165.0,
        'amount_with_trustee' => 165.0,
        'premium' => 0,
        'currency' => 'CZK',
      ],
    ]
*/
```

### Errors

Client can failures on so many levels. 

- **Invalid request** – when you trying submit invalid request, client throws `InvalidRequestException`,
- **Connection problem** – offline, invalid URL, server problem, etc., client throws `ConnectionException`,
- **Server response error** – API server is working, but can't finish your request, domain doesn't exists, etc., client
    throws `ResponseErrorException`,    
- **Another problem with response** – response is missing required fiels, etc., client throws `InvalidResponseException`.

`ResponseErrorException` (and all descendants Exceptions) contains whole response, use:
```php
$exception->getResponse();
```

### Response

Response is being wrapped to `Response` envelope for better handling. Response always contains an array.

#### `getData(): array`

Returns all data from Response as received from API server.

#### `getItem(string $name)`

Returns one item from Response array. If item doesn't exists, returns `null`.

It allows only access to first-level of response array (no recursive access into deep structured data).

#### `hasItem(string $name): bool`

Returns `true` when item exists in Response, `false` otherwise.

#### `getMandatoryItem(string $name)`

Returns one item from Response array. If item doesn't exists, throws `InvalidResponseException`.

### Factory shortcuts

Creating `Client` instance requires pre-create `Credentials` first.

```php
$credentials = new \Redbitcz\SubregApi\Credentials('microsoft', 'password');
$client = new \Redbitcz\SubregApi\Client($credentials);
```

It's can be simplified by `Factory` helper:

```php
$client = \Redbitcz\SubregApi\Factory::createClient('microsoft', 'password');
```

If you are using [administrator access](https://subreg.cz/en/settings/admins/) to account, use:

```php
$client = \Redbitcz\SubregApi\Factory::createClientForAdministrator('microsoft', 'gates', 'password');
```

### Example
```php
$client = \Redbitcz\SubregApi\Factory::createClient('microsoft', 'password');

$response = $client->call('Info_Domain', ['domain' => 'subreg.cz']);

echo "Domain {$response->getItem('name')} is expiring at {$response->getItem('exDate')}.";
// Domain subreg.cz is expiring at 2023-04-22.
```

## Usage Context access

At first create `Context` object instance. Use `Factory` helper for simple create it:

```php
$context = \Redbitcz\SubregApi\Factory::createContext('microsoft', 'password');
```

With `Context` object you can access to API resources comfortably by
[fluent-like properties](https://dev.to/mofiqul/fluent-interface-and-method-chaining-in-php-and-javascript-251c).

```php
foreach ($context->domain()->list() as $domain) {
    echo "Domain {$domain->getName()} is expiring at {$domain->getExpire()->format('Y-m-d')}.\n";
}
// Domain my-first-domain.cz is expiring at 2023-04-22.
// Domain my-second-domain.cz is expiring at 2020-01-01.
// Domain my-third-domain.cz is expiring at 2021-08-30.
// Domain my-fourth-domain.cz is expiring at 2022-01-15.
// Domain my-fifth-domain.cz is expiring at 2020-05-13.
// Domain my-sixth-domain.cz is expiring at 2020-05-13.
```

**NOTE:** Context access is covering only few most-used of [Subreg.cz API commands](https://subreg.cz/manual/).

## Work in progress
Package is still under development.

### Future examples in vision

Bulk renew of expiring domains:
```php
$expiringDomains = $context->domain()->list()->filter(['expire < ' => new DateTime('+ 1 month')]); 

foreach ($expiringDomains as $domain) {
    $order = $domain->renew(1); // 1 year
    echo "Domain {$domain->getName()} is renewed by order ID: {$order->getId()}.\n";
}
// Domain my-second-domain.cz is renewed by order ID: 12345000.
// Domain my-fifth-domain.cz is renewed by order ID: 12345001.
// Domain my-sixth-domain.cz is renewed by order ID: 12345002.
```

Remove deprecated SPF record from domains zones for 3th+ level domain:
```php
foreach ($context->domain()->list() as $domain) {
    foreach ($domain->dns()->list()->filter(['!isType' => 'SPF', '@getFqn() ~ ' => '/\w+\.\w+\.\w+$/']) as $dnsRecord) {
        $dnsRecord->delete();
        echo "Removed SPF record for {$dnsRecord->getFqn()}.\n";
    }
}
// Removed SPF record for subdomain.my-first-domain.cz.
// Removed SPF record for cluster-1.region-eu.my-thitd-domain.cz.
```