<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Schema;

use Nette\Schema\Elements\Structure;
use Nette\Schema\Processor;
use Soukicz\SubregApi\Exception\SchemaItemMissingException;

trait SchemaObject
{
    private static $STRUCTURE_KEY_DELIMITER = '.';

    /** @var array */
    private $data = [];

    abstract public function defineSchema(): Structure;

    protected function setData(array $data): void
    {
        $this->data = (new Processor())->process($this->defineSchema()->castTo('array'), $data);
    }

    public function getData(): array
    {
        return $this->data;
    }

    protected function getItem(string $key)
    {
        try {
            return $this->getMandatoryItem($key);
        } catch (SchemaItemMissingException $e) {
            return null;
        }
    }

    protected function getMandatoryItem(string $key)
    {
        return $this->getItemRecursive(explode(self::$STRUCTURE_KEY_DELIMITER, $key), $this->data);
    }

    private function getItemRecursive(array $keys, array $data, array $path = [])
    {
        $key = array_shift($keys);
        $path[] = $key;

        if (array_key_exists($key, $data) === false) {
            throw new SchemaItemMissingException(
                sprintf(
                    "Unable to get '%s' item from scheme because '%s' does not exists",
                    implode('.', array_merge($path, $keys)),
                    implode('.', $path)
                )
            );
        }
        if (count($keys)) {
            if (is_array($data[$key])) {
                return $this->getItemRecursive($keys, $data[$key], $path);
            }

            throw new SchemaItemMissingException(
                sprintf(
                    "Unable to get '%s' item from scheme because '%s' is not array but %s",
                    implode('.', array_merge($path, $keys)),
                    implode('.', $path),
                    gettype($data[$key])
                )
            );
        }

        return $data[$key];
    }
}
