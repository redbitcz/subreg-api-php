<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Collection;

use Countable;
use Generator;
use IteratorAggregate;
use Redbitcz\SubregApi\Context\Context;
use Redbitcz\SubregApi\Context\ContextAware;
use Redbitcz\SubregApi\Entity\DnsRecord;
use Redbitcz\SubregApi\Response\Response;

class DnsZone implements IteratorAggregate, Countable
{
    use ContextAware;

    /** @var string */
    private $domain;

    /** @var array */
    private $records;

    public function __construct(string $domain, array $records, ?Context $context = null)
    {
        $this->domain = $domain;
        $this->records = $records;
        $this->setContext($context);
    }

    /**
     * @return Generator|DnsRecord[]
     */
    public function getIterator(): Generator
    {
        foreach ($this->records as $record) {
            yield DnsRecord::fromResponseItem($this->domain, $record, $this->getContext());
        }
    }

    public function count(): int
    {
        return count($this->records);
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getData(): array
    {
        return $this->records;
    }

    public static function fromResponse(Response $response, ?Context $context = null): self
    {
        return new self(
            $response->getMandatoryItem('domain'),
            $response->getMandatoryItem('records') ?? [],
            $context
        );
    }
}
