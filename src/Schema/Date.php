<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Schema;

use DateTimeImmutable;
use DateTimeZone;
use Nette\Schema\Context;

class Date extends DateTime
{
    public function __construct($format = 'Y-m-d', ?DateTimeZone $timeZone = null)
    {
        parent::__construct($format, $timeZone);
    }

    public function normalize($value, Context $context): ?DateTimeImmutable
    {
        $normalized = parent::normalize($value, $context);

        if ($normalized instanceof DateTimeImmutable) {
            $normalized = $normalized->setTime(0, 0, 0, 0);
        }

        return $normalized;
    }
}
