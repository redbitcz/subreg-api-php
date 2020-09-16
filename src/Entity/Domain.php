<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Entity;

use DateTimeImmutable;
use Soukicz\SubregApi\Context;
use Soukicz\SubregApi\ContextAware;

/*
    <xs:complexType name="Domains_List_Domain">
        <xs:sequence>
            <xs:element name="name" type="xs:string"/>
            <xs:element name="expire" type="xs:string"/>
            <xs:element name="autorenew" type="xs:integer"/>
        </xs:sequence>
    </xs:complexType>
*/

class Domain
{
    use ContextAware;

    public const AUTORENEW_EXPIRE = 0;
    public const AUTORENEW_AUTORENEW = 1;
    public const AUTORENEW_RENEWONCE = 2;

    /** @var string */
    private $name;
    /** @var DateTimeImmutable */
    private $expire;
    /** @var int */
    private $autorenew;

    public function __construct(string $name, DateTimeImmutable $expire, int $autorenew, ?Context $context = null)
    {
        $this->name = $name;
        $this->expire = $expire;
        $this->autorenew = $autorenew;
        $this->setContext($context);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExpire(): DateTimeImmutable
    {
        return $this->expire;
    }

    public function getAutorenew(): int
    {
        return $this->autorenew;
    }

    /**
     * @param array $item
     * @param Context|null $context
     * @return static
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function fromArray(array $item, ?Context $context = null): self
    {
        return new self(
            $item['name'],
            new DateTimeImmutable($item['expire']),
            (int)$item['autorenew']
        );
    }
}
