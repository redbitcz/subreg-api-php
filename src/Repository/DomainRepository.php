<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Repository;

use Redbitcz\SubregApi\Collection\Domains;
use Redbitcz\SubregApi\Context\Context;
use Redbitcz\SubregApi\Entity\DomainCheck;
use Redbitcz\SubregApi\Entity\DomainInfo;
use Redbitcz\SubregApi\Entity\DomainInfoCz;

class DomainRepository
{
    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function check(string $domain): DomainCheck
    {
        $response = $this->context->getClient()->call('Check_Domain', ['domain' => $domain]);
        return DomainCheck::fromResponse($response, $this->context);
    }

    public function list(): Domains
    {
        $response = $this->context->getClient()->call('Domains_List');
        return Domains::fromResponse($response, $this->context);
    }

    public function info(string $domain): DomainInfo
    {
        $response = $this->context->getClient()->call('Info_Domain', ['domain' => $domain]);
        return DomainInfo::fromResponse($response, $this->context);
    }

    public function infoCz(string $domain): DomainInfoCz
    {
        $response = $this->context->getClient()->call('Info_Domain_CZ', ['domain' => $domain]);
        return DomainInfoCz::fromResponse($response, $this->context);
    }
}
