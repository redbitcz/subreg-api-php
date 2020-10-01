<?php

declare(strict_types=1);

namespace Soukicz\SubregApi;

class Helpers
{
    /**
     * Transfer int-like string value to int, other types leave as is to corrent validation
     * SOAP transfers all values as a string
     * @param mixed $value
     * @return int|mixed
     */
    public static function handleSoapInt($value)
    {
        if (is_string($value) && preg_match('/^\d+$/D', $value)) {
            $value = (int)$value;
        }

        return $value;
    }
}
