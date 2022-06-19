<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Context;

use DateTimeZone;
use Redbitcz\SubregApi\Client;
use Redbitcz\SubregApi\Repository\DnsRepository;
use Redbitcz\SubregApi\Repository\DomainRepository;

class Context
{
    /** Default TimeZone location – Subreg.cz has HQ located in Prague, Subreg API uses Prague local time */
    private const DEFAULT_TIMEZONE = 'Europe/Prague';

    /** @var Client */
    private $client;

    /** @var DateTimeZone */
    private $timeZone;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->timeZone = new DateTimeZone(self::DEFAULT_TIMEZONE);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return $this
     */
    public function setTimeZone(DateTimeZone $timeZone): self
    {
        $this->timeZone = $timeZone;
        return $this;
    }

    public function getTimeZone(): DateTimeZone
    {
        return $this->timeZone;
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
