<?php

declare(strict_types=1);


namespace Redbitcz\SubregApi\Entity;

use stdClass;

interface SchemaEntity
{
    public function getItem(string $key);

    public function hasItem(string $key): bool;

    public function getMandatoryItem(string $key);

    public function getData(): stdClass;

    public function toArray(): array;
}
