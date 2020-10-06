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
 * - string    domain    Domain name
 * - array    contacts    Contacts (admin,tech,bill) associated with domain name
 *     - string    subregid    Contact ID from Subreg Database
 *     - string    registryid    Contact ID directly from Registry
 * - array    hosts    Nameservers
 * - string    registrant    Domain name owner
 * - string    crDate    Date of domain creation
 * - string    trDate    Date of domain last transfer
 * - string    upDate    Date of domain last update
 * - string    exDate    Date of domain expiration
 * - string    authid    Domain password for transfers
 * - array    status    Domain status (ok,clientTransferProhibited,etc..)
 * - int    autorenew    Domain autorenew setting (0 - EXPIRE | 1 - AUTORENEW | 2 - RENEWONCE)
 * - int    premium    0 - domain is not premium, 1 - domain is premium with premium price
 * - decimal    price    Domain price
 */
class DomainInfo implements SchemaEntity
{
    use SchemaObject;
    use ContextAware;

    public const AUTORENEW_EXPIRE = 0;
    public const AUTORENEW_AUTORENEW = 1;
    public const AUTORENEW_RENEWONCE = 2;
    public const PREMIUM_NOT_PREMIUM = 0;
    public const PREMIUM_IS_PREMIUM = 1;

    public function __construct(array $data, ?Context $context)
    {
        $this->setData($data);
        $this->setContext($context);
    }

    public function defineSchema(): Structure
    {
        $domainContact = Schema::structure(
            [
                'subregid' => Expect::string(),
                'registryid' => Expect::string(),
            ]
        );

        $domainDsdata = Schema::structure(
            [
                'tag' => Expect::string()->required(),
                'alg' => Expect::string()->required(),
                'digest_type' => Expect::string()->required(),
                'digest' => Expect::string()->required(),
            ]
        );
        $domainOptions = Schema::structure(
            [
                'nsset' => Expect::string(),
                'keyset' => Expect::string(),
                'dsdata' => Expect::listOf($domainDsdata),
                'keygroup' => Expect::string(),
                'quarantined' => Expect::string(),
            ]
        );
        return Schema::structure(
            [
                'domain' => Expect::string()->required(),
                'contacts' => Schema::structure(
                    [
                        'admin' => Expect::listOf($domainContact),
                        'tech' => Expect::listOf($domainContact),
                        'bill' => Expect::listOf($domainContact),
                    ]
                ),
                'hosts' => Expect::listOf(Expect::string()),
                'registrant' => $domainContact,
                'exDate' => Schema::date()->required(),
                'crDate' => Schema::date(),
                'trDate' => Schema::date(),
                'upDate' => Schema::date(),
                'authid' => Expect::string(),
                'status' => Expect::listOf(Expect::string()),
                'rgp' => Expect::listOf(Expect::string()),
                'autorenew' => Expect::anyOf(
                    self::AUTORENEW_EXPIRE,
                    self::AUTORENEW_AUTORENEW,
                    self::AUTORENEW_RENEWONCE
                )
                    ->before([Helpers::class, 'soapInt'])
                    ->required(),

                'premium' => Expect::anyOf(self::PREMIUM_NOT_PREMIUM, self::PREMIUM_IS_PREMIUM),
                'price' => Expect::float(),
                'whoisproxy' => Expect::int()->before([Helpers::class, 'soapInt']),
                'trustee' => Expect::int(),
                'options' => $domainOptions,
            ]
        );
    }

    public function getName(): string
    {
        return $this->getMandatoryItem('domain');
    }

    public static function fromResponse(Response $response, ?Context $context = null): self
    {
        return new self($response->getData(), $context);
    }
}
