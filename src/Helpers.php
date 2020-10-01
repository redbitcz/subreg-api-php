<?php

declare(strict_types=1);

namespace Soukicz\SubregApi;

class Helpers
{
    /**
     * Transfer int-like string value to int, other types leave as is to correct validation
     * SOAP transfers all values as a string
     * @param mixed $value
     * @return int|mixed
     */
    public static function soapInt($value)
    {
        if (is_string($value) && preg_match('/^\d+$/D', $value)) {
            $value = (int)$value;
        }

        return $value;
    }
    /**
     * Transfer fload-like string value to float, other types leave as is to correct validation
     * SOAP transfers all values as a string
     * @param mixed $value
     * @return float|mixed
     */
    public static function soapFloat($value)
    {
        if (is_string($value) && preg_match('/^\d+\.?\d*$/D', $value)) {
            $value = (float)$value;
        }

        return $value;
    }
}
