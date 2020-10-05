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
- **low-level access**: just call commands and get raw response – the Package only handle Request authentication,
    Connection or Server errors and check basic Envelope od Response data. 
- **context access**: full service access which is allow traversing over data, checks Request and Response data by
    strict Schema, access to all properties via typed Getters.

For more information see documentation bellow.

## Authentication & Environment
### Authentication

For authentication to API use `Credentials` class:

```php
$credentials = new \Redbitcz\SubregApi\Credentials('microsoft', 'password');
```

If you are using [Subreg administrator access](https://subreg.cz/en/settings/admins/) to account, use shortcut:

```php
$credentials = \Redbitcz\SubregApi\Credentials::forAdministrator('microsoft', 'gates', 'password');
``` 

For more information about [administrator access](https://subreg.cz/en/settings/admins/) visit
[Subreg API Login documentation page](https://subreg.cz/manual/?cmd=Login).

### Environment

Package is process all requests to Production Environment.

If you need try your App to [test Environment](https://subreg.cz/manual/?cmd=Main), you can change URL Endpoint:

```php
$credentials = new \Redbitcz\SubregApi\Credentials('microsoft', 'password', 'https://ote-soap.subreg.cz/cmd.php');
```  

## Usage

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



 
```php
require __DIR__ . '/autoload.php';

use Redbitcz\SubregApi;

$context = SubregApi\Factory::createContext('username', 'password', '/temp');

foreach($context->domain()->list() as $domain) {
    echo $domain->getName() . PHP_EOL;

    foreach($domain->getDnsZone() as $dnsRecord) {
        if($dnsRecord->isType('SPF')) {
            $dnsRecord->delete();
        }           
    }   
}
```