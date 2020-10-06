<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi;

use stdClass;

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
     * Transfer float-like string value to float, other types leave as is to correct validation
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

    /**
     * Recursive clone any type of stdClass, array and stdClass objects was cloned recursively
     * @param stdClass|array $object
     * @return stdClass|array
     */
    public static function deepClone($object)
    {
        if ($object instanceof stdClass) {
            $object = clone $object;
            foreach (get_object_vars($object) as $index => $item) {
                if (is_array($item) || $item instanceof stdClass) {
                    $object->$index = self::deepClone($item);
                }
            }
        } elseif (is_array($object)) {
            foreach ($object as $index => $item) {
                if (is_array($item) || $item instanceof stdClass) {
                    $object[$index] = self::deepClone($item);
                }
            }
        }

        return $object;
    }


    /**
     * Recursive cast any type to array, array and stdClass objects was casted recursively
     * @param array|stdClass|mixed $var
     * @return array
     */
    public static function toArray($var): array
    {
        $array = (array)$var;

        foreach ($array as $index => $item) {
            if (is_array($item) || $item instanceof stdClass) {
                $array[$index] = self::toArray($item);
            }
        }

        return $array;
    }
}
