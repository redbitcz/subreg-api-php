<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Collection;

use Countable;
use Generator;
use IteratorAggregate;
use Soukicz\SubregApi\Context\Context;
use Soukicz\SubregApi\Context\ContextAware;
use Soukicz\SubregApi\Entity\Domain;
use Soukicz\SubregApi\Response\Response;

class Domains implements IteratorAggregate, Countable
{
    use ContextAware;

    /** @var array */
    private $domains;

    public function __construct(array $domains, ?Context $context = null)
    {
        $this->domains = $domains;
        $this->setContext($context);
    }

    /**
     * @return Generator|Domain[]
     */
    public function getIterator(): Generator
    {
        foreach ($this->domains as $domain) {
            yield Domain::fromResponseItem($domain, $this->getContext());
        }
    }

    public function count(): int
    {
        return count($this->domains);
    }

    public static function fromResponse(Response $response, ?Context $context = null): self
    {
        return new self($response->getMandatoryField('domains') ?? [], $context);
    }
}
