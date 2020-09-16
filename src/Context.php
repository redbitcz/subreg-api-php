<?php

declare(strict_types=1);

namespace Soukicz\SubregApi;

use Soukicz\SubregApi\Iterator\Domains;

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

    public function getDomainList(): Domains
    {
        $response = $this->client->call('Domains_List');
        return new Domains($response->getMandatoryField('domains'), $this);
    }
}
