<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Entity;

use Soukicz\SubregApi\Context;
use Soukicz\SubregApi\ContextAware;

/*
 *  <xs:complexType name="Check_Domain_Price">
 *    <xs:sequence>
 *      <xs:element name="amount" type="xs:decimal"/>
 *      <xs:element name="amount_with_trustee" type="xs:decimal" minOccurs="0"/>
 *      <xs:element name="premium" type="xs:integer" minOccurs="0"/>
 *      <xs:element name="currency" type="xs:string"/>
 *    </xs:sequence>
 *  </xs:complexType>
 *  <xs:complexType name="Check_Domain_Data">
 *    <xs:sequence>
 *      <xs:element name="name" type="xs:string"/>
 *      <xs:element name="avail" type="xs:integer"/>
 *      <xs:element name="existing_claim_id" type="xs:string" minOccurs="0"/>
 *      <xs:element name="price" type="Check_Domain_Price" minOccurs="0"/>
 *    </xs:sequence>
 *  </xs:complexType>
 */

class DomainCheck
{
    use ContextAware;

    public function __construct(string $name, int $avail, ?string $existingClaimId, ?Context $context = null)
    {
        $this->setContext($context);
    }
}
