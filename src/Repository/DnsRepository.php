<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Repository;

use Soukicz\SubregApi\Collection\DnsRecords;
use Soukicz\SubregApi\Context\Context;

class DnsRepository
{
    /** @var Context */
    private $context;
    /** @var string */
    private $domain;

    public function __construct(string $domain, Context $context)
    {
        $this->context = $context;
        $this->domain = $domain;
    }

    public function list(): DnsRecords
    {
        $response = $this->context->getClient()->call('Get_DNS_Zone', ['domain' => $this->domain]);
        return DnsRecords::fromResponse($response, $this->context);
    }
}
