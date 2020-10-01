<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Repository;

use Soukicz\SubregApi\Context;
use Soukicz\SubregApi\Iterator\Domains;

class DomainRepository
{
    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function list(): Domains
    {
        $response = $this->context->getClient()->call('Domains_List');
        return Domains::fromResponse($response, $this->context);
    }
}
