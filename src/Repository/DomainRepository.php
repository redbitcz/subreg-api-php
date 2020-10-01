<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Repository;

use Soukicz\SubregApi\Context;
use Soukicz\SubregApi\Entity\DomainCheck;
use Soukicz\SubregApi\Iterator\Domains;

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
}
