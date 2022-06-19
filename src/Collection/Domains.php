<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Collection;

use Countable;
use Generator;
use IteratorAggregate;
use Redbitcz\SubregApi\Context\Context;
use Redbitcz\SubregApi\Context\ContextAware;
use Redbitcz\SubregApi\Entity\Domain;
use Redbitcz\SubregApi\Entity\SchemaEntity;
use Redbitcz\SubregApi\Response\Response;

class Domains implements IteratorAggregate, Countable
{
    use ContextAware;

    /** @var array */
    private $domains;

    public function __construct(array $domains, ?Context $context = null)
    {
        $this->setContext($context);

        $this->domains = $domains;
    }

    /**
     * @return Generator|SchemaEntity[]|Domain[]
     */
    public function getIterator(): Generator
    {
        foreach ($this->domains as $domain) {
            yield Domain::fromResponseItem($domain, $this->getContext());
        }
    }

    public function filter(array $expression): Filter
    {
        return Filter::createForExpression($this, $expression);
    }

    public function callbackFilter(callable $filter): Filter
    {
        return Filter::createForCallback($this, $filter);
    }

    public function count(): int
    {
        return count($this->domains);
    }

    public function getData(): array
    {
        return $this->domains;
    }

    public static function fromResponse(Response $response, ?Context $context = null): self
    {
        return new self($response->getMandatoryItem('domains') ?? [], $context);
    }
}
