# PHP wrapper for Subreg.cz SOAP API

> WORK IN PROGRESS

## About API
- [**Subreg.cz API manual**](https://subreg.cz/manual/)

## Concept
```php
require __DIR__ . '/autoload.php';

use Soukicz\SubregApi;

$context = SubregApi\Factory::createContext('username', 'password', '/temp');

foreach($context->getDomainList() as $domain) {
    echo $domain->getName() . PHP_EOL;

    foreach($domain->getDnsZone() as $dnsRecord) {
        if($dnsRecord->isType('SPF')) {
            $dnsRecord->delete();
        }           
    }   
}
```