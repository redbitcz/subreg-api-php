<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Entity;

use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Redbitcz\SubregApi\Context\Context;
use Redbitcz\SubregApi\Context\ContextAware;
use Redbitcz\SubregApi\Helpers;
use Redbitcz\SubregApi\Schema\Schema;
use Redbitcz\SubregApi\Schema\SchemaObject;

/**
 * ## Schema
 * - string    domain    Registered domain for which records are returned
 * - array    records    List of records identified by IDs
 *     - int    id    ID of the record
 *     - string    name    Hostname
 *     - string    type    Type of DNS record
 *     - string    content    Value of this record (IP address, hostname, text value etc.)
 *     - int    prio    Priority of this record (MX records only)
 *     - int    ttl    TTL value
 */
class DnsRecord implements SchemaEntity
{
    use SchemaObject;
    use ContextAware;

    /** @var string */
    private $domain;

    public function __construct(string $domain, array $data, ?Context $context = null)
    {
        $this->setContext($context);

        $this->domain = $domain;
        $this->setData($data);
    }

    public function defineSchema(): Structure
    {
        return Schema::structure(
            [
                'id' => Expect::int()->before([Helpers::class, 'soapInt'])->required(),
                'name' => Expect::string()->required(),
                'type' => Expect::string()->required(),
                'content' => Expect::string(),
                'prio' => Expect::int()->before([Helpers::class, 'soapInt']),
                'ttl' => Expect::int()->before([Helpers::class, 'soapInt']),
            ]
        );
    }

    public function getId(): int
    {
        return $this->getMandatoryItem('id');
    }

    public function getName(): string
    {
        return $this->getMandatoryItem('name');
    }

    public function getType(): string
    {
        return $this->getMandatoryItem('type');
    }

    public function isType(string $type): bool
    {
        return strcasecmp($this->getMandatoryItem('type'), $type) === 0;
    }

    public function getContent(): ?string
    {
        return $this->getItem('content');
    }

    public function getPriority(): ?int
    {
        return $this->getItem('prio');
    }

    public function getTtl(): ?int
    {
        return $this->getItem('ttl');
    }

    public static function fromResponseItem(string $domain, array $data, ?Context $context = null): self
    {
        return new self($domain, $data, $context);
    }
}
