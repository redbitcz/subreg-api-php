<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Schema;

use DateTimeZone;
use Nette\Schema\Elements\Structure;

class Schema
{
    public static function date($format = 'Y-m-d', ?DateTimeZone $timeZone = null): Date
    {
        return new Date($format, $timeZone);
    }

    public static function dateTime($format = 'Y-m-d H:i:s', ?DateTimeZone $timeZone = null): DateTime
    {
        return new DateTime($format, $timeZone);
    }

    /**
     * Create Structure with allowed unexpected fields by default
     *
     * @param array $items
     * @return Structure
     */
    public static function structure(array $items): Structure
    {
        return (new Structure($items))->otherItems();
    }
}
