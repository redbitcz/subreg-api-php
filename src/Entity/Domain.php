<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Entity;

use DateTimeImmutable;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Soukicz\SubregApi\Context;
use Soukicz\SubregApi\ContextAware;
use Soukicz\SubregApi\Helpers;
use Soukicz\SubregApi\Schema;
use Soukicz\SubregApi\Schema\SchemaObject;

/*
 *  <xs:complexType name="Domains_List_Domain">
 *      <xs:sequence>
 *          <xs:element name="name" type="xs:string"/>
 *          <xs:element name="expire" type="xs:string"/>
 *          <xs:element name="autorenew" type="xs:integer"/>
 *      </xs:sequence>
 *  </xs:complexType>
*/

class Domain
{
    use SchemaObject;
    use ContextAware;

    public const AUTORENEW_EXPIRE = 0;
    public const AUTORENEW_AUTORENEW = 1;
    public const AUTORENEW_RENEWONCE = 2;

    public function __construct(array $data, ?Context $context = null)
    {
        $this->setData($data);
        $this->setContext($context);
    }

    public function defineSchema(): Structure
    {
        return Expect::structure(
            [
                'name' => Expect::string()->required(),
                'expire' => (new Schema\Date())->required(),
                'autorenew' => Expect::anyOf(
                    self::AUTORENEW_EXPIRE,
                    self::AUTORENEW_AUTORENEW,
                    self::AUTORENEW_RENEWONCE
                )
                    ->before([Helpers::class, 'handleSoapInt'])
                    ->castTo('int')
                    ->required(),
            ]
        );
    }

    public function getName(): string
    {
        return $this->getMandatoryItem('name');
    }

    public function getExpire(): DateTimeImmutable
    {
        return $this->getMandatoryItem('expire');
    }

    public function getAutorenew(): int
    {
        return $this->getMandatoryItem('autorenew');
    }

    public static function fromResponseItem(array $data, ?Context $context = null): self
    {
        return new self($data, $context);
    }
}
