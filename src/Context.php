<?php

declare(strict_types=1);

namespace Soukicz\SubregApi;

use Soukicz\SubregApi\Repository\DomainRepository;

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

    public function domains(): DomainRepository
    {
        return new DomainRepository($this);
    }

}
