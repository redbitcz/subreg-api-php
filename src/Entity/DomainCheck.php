<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Entity;

use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Soukicz\SubregApi\Context;
use Soukicz\SubregApi\ContextAware;
use Soukicz\SubregApi\Helpers;
use Soukicz\SubregApi\Response\Response;
use Soukicz\SubregApi\Schema\SchemaObject;

/**
 *  string    name    Domain name
 *  string    avail    0 - not available, 1 - available
 *  string    existing_claim_id    ID of existing TMCH claim - domain with claim id isn't possible register via API
 *  array    price
 *      string    amount    Total domain price (premium price included)
 *      string    amount_with_trustee    Total domain price with trustee (if domain required), premium price included
 *      string    premium    0 - domain is not premium, 1 - domain is premium with premium price
 *      string    currency    Currency of this price
 */
class DomainCheck
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
        return Expect::structure(
            [
                'name' => Expect::string()->required(),
                'avail' => Expect::anyOf(self::AVAILABILITY_NOT_AVAILABLE, self::AVAILABILITY_IS_AVAILABLE)
                    ->before([Helpers::class, 'soapInt'])->required(),
                'existing_claim_id' => Expect::string()->nullable(),
                'price' => Expect::structure(
                    [
                        'amount' => Expect::float()->before([Helpers::class, 'soapFloat'])->required(),
                        'currency' => Expect::string()->required(),
                        'premium' => Expect::anyOf(self::PREMIUM_NOT_PREMIUM, self::PREMIUM_IS_PREMIUM)
                            ->before([Helpers::class, 'soapInt']),
                        'amount_with_trustee' => Expect::float()->before([Helpers::class, 'soapFloat']),
                    ]
                )->castTo('array')
            ]
        );
    }

    public function getName(): string
    {
        return $this->getItem('name');
    }

    public function getAvailability(): int
    {
        return $this->getItem('avail');
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
