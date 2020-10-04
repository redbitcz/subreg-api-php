<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Context;

use Redbitcz\SubregApi\Client;
use Redbitcz\SubregApi\Repository\DnsRepository;
use Redbitcz\SubregApi\Repository\DomainRepository;

class Context
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function domain(): DomainRepository
    {
        return new DomainRepository($this);
    }

    public function dns(string $domain): DnsRepository
    {
        return new DnsRepository($domain, $this);
    }
}
