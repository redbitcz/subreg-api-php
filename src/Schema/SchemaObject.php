<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Schema;

use Nette\Schema\Elements\Structure;
use Nette\Schema\Processor;
use Redbitcz\SubregApi\Exception\SchemaItemMissingException;
use Redbitcz\SubregApi\Helpers;
use stdClass;

trait SchemaObject
{
    private static $STRUCTURE_KEY_DELIMITER = '.';

    /** @var stdClass */
    private $data;

    abstract public function defineSchema(): Structure;

    protected function setData(array $data): void
    {
        $this->data = (new Processor())->process($this->defineSchema(), $data);
    }

    public function getData(): stdClass
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return Helpers::toArray($this->data);
    }

    public function getItem(string $key)
    {
        try {
            return $this->getMandatoryItem($key);
        } catch (SchemaItemMissingException $e) {
            return null;
        }
    }

    public function hasItem(string $key): bool
    {
        try {
            $this->getMandatoryItem($key);
            return true;
        } catch (SchemaItemMissingException $e) {
            return false;
        }
    }

    public function getMandatoryItem(string $key)
    {
        return $this->getItemRecursive(explode(self::$STRUCTURE_KEY_DELIMITER, $key), $this->data);
    }

    private function getItemRecursive(array $keys, stdClass $data, array $path = [])
    {
        $key = array_shift($keys);
        $path[] = $key;

        if (property_exists($data, $key) === false) {
            throw new SchemaItemMissingException(
                sprintf(
                    "Unable to get '%s' item from scheme because '%s' does not exists",
                    implode('.', array_merge($path, $keys)),
                    implode('.', $path)
                )
            );
        }
        if (count($keys)) {
            if ($data->$key instanceof stdClass) {
                return $this->getItemRecursive($keys, $data->$key, $path);
            }

            throw new SchemaItemMissingException(
                sprintf(
                    "Unable to get '%s' item from scheme because '%s' is not structure but %s",
                    implode('.', array_merge($path, $keys)),
                    implode('.', $path),
                    gettype($data->$key)
                )
            );
        }

        return $data->$key;
    }
}
