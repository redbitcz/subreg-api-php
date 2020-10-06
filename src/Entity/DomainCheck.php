<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Entity;

use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Redbitcz\SubregApi\Context\Context;
use Redbitcz\SubregApi\Context\ContextAware;
use Redbitcz\SubregApi\Helpers;
use Redbitcz\SubregApi\Response\Response;
use Redbitcz\SubregApi\Schema\Schema;
use Redbitcz\SubregApi\Schema\SchemaObject;

/**
 * ## Schema
 * - string    name    Domain name
 * - int    avail    0 - not available, 1 - available
 * - string    existing_claim_id    ID of existing TMCH claim - domain with claim id isn't possible register via API
 * - array    price
 *     - float    amount    Total domain price (premium price included)
 *     - float    amount_with_trustee    Total domain price with trustee (if domain required), premium price included
 *     - int    premium    0 - domain is not premium, 1 - domain is premium with premium price
 *     - string    currency    Currency of this price
 */
class DomainCheck implements SchemaEntity
{
    use SchemaObject;
    use ContextAware;

    public const AVAILABILITY_NOT_AVAILABLE = 0;
    public const AVAILABILITY_IS_AVAILABLE = 1;
    public const PREMIUM_NOT_PREMIUM = 0;
    public const PREMIUM_IS_PREMIUM = 1;

    public function __construct(array $data, ?Context $context)
    {
        $this->setData($data);
        $this->setContext($context);
    }

    public function defineSchema(): Structure
    {
        return Schema::structure(
            [
                'name' => Expect::string()->required(),
                'avail' => Expect::anyOf(self::AVAILABILITY_NOT_AVAILABLE, self::AVAILABILITY_IS_AVAILABLE)
                    ->before([Helpers::class, 'soapInt'])->required(),
                'existing_claim_id' => Expect::string()->nullable(),
                'price' => Schema::structure(
                    [
                        'amount' => Expect::float()->before([Helpers::class, 'soapFloat'])->required(),
                        'currency' => Expect::string()->required(),
                        'premium' => Expect::anyOf(self::PREMIUM_NOT_PREMIUM, self::PREMIUM_IS_PREMIUM)
                            ->before([Helpers::class, 'soapInt']),
                        'amount_with_trustee' => Expect::float()->before([Helpers::class, 'soapFloat']),
                    ]
                )
            ]
        );
    }

    public function getName(): string
    {
        return $this->getMandatoryItem('name');
    }

    public function getAvailability(): int
    {
        return $this->getMandatoryItem('avail');
    }

    public function isAvailable(): bool
    {
        return $this->getAvailability() === self::AVAILABILITY_IS_AVAILABLE;
    }

    public function getPriceAmount(): ?float
    {
        return $this->getItem('price.amount');
    }

    public function getPriceTrusteeAmount(): ?float
    {
        return $this->getItem('price.amount_with_trustee');
    }

    public function getPriceCurrency(): ?string
    {
        return $this->getItem('price.currency');
    }

    public function getPricePremuim(): int
    {
        return $this->getItem('price.premium');
    }

    public function isPricePremuim(): bool
    {
        return $this->getPricePremuim() === self::PREMIUM_IS_PREMIUM;
    }

    public static function fromResponse(Response $response, ?Context $context = null): self
    {
        return new self($response->getData(), $context);
    }
}
