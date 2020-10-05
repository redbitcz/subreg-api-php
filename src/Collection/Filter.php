<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Collection;

use Generator;
use IteratorAggregate;
use Redbitcz\SubregApi\Entity\SchemaEntity;
use Redbitcz\SubregApi\Exception\LogicException;
use Redbitcz\SubregApi\Exception\SchemaItemMissingException;
use Traversable;

class Filter implements IteratorAggregate
{
    public const OPER_EXISTS = ':exists:';
    public const OPER_EQUAL = '=';
    public const OPER_NOT_EQUAL = '!=';
    public const OPER_IDENTICAL = '===';
    public const OPER_NOT_IDENTICAL = '!==';
    public const OPER_LESS_THAN = '<';
    public const OPER_GREATER_THAN = '>';
    public const OPER_LESS_OR_EQUAL = '<=';
    public const OPER_GREATER_OR_EQUAL = '>=';
    public const OPER_PREG = '~';

    /** @var Traversable */
    private $collection;

    /** @var callable */
    private $filter;

    public function __construct(Traversable $collection, callable $filter)
    {
        $this->collection = $collection;
        $this->filter = $filter;
    }

    /** @return Generator|SchemaEntity[] */
    public function getIterator(): Generator
    {
        foreach ($this->collection as $key => $item) {
            if (($this->filter)($item, $key) === true) {
                yield $item;
            }
        }
    }

    public static function createForCallback(Traversable $collection, callable $filter): self
    {
        return new self($collection, $filter);
    }

    public static function createForExpression(Traversable $collection, array $expression): self
    {
        $expressions = [];
        foreach ($expression as $key => $expected) {
            if (is_string($key) === false || is_string($expected) === false) {
                throw new LogicException("Filter expression must be associative array <string> => <string>");
            }

            // Parse expression to key + operator ('date < ' => 'date' + '<')
            $result = preg_match(
                '/^\s*(?<command>[!@])?\s*(?<key>[.\w]+)(?:\((?<argument>\w+|\$)?\))?\s*(?<operator>:\w:|[=<>!~]+)?\s*$/D',
                $key,
                $matches
            );
            if ($result === 0) {
                throw new LogicException("Filter format expression '$key' is invalid");
            }
            $key = $matches['key'];
            $operator = $matches['operator'] ?? self::OPER_EQUAL;
            $expressions[] = [$key, $operator, $expected];
        }

        // Create filter closure with parsed expressions
        $filter = static function (SchemaEntity $item) use ($expressions): bool {
            $result = true;
            foreach ($expressions as [$key, $operator, $expected]) {
                $result = $result && self::filterByExpression($item, $key, $operator, $expected);
            }
            return $result;
        };

        return new self($collection, $filter);
    }

    private static function filterByExpression(SchemaEntity $item, string $key, string $operator, $expected): bool
    {
        $value = null;
        $keyExists = true;
        try {
            $value = $item->getMandatoryItem($key);
        } catch (SchemaItemMissingException $e) {
            $keyExists = false;
        }

        // Operator ['key :exists:' => true]
        if ($operator === self::OPER_EXISTS) {
            return $keyExists === (bool)$expected;
        }

        // Key is missing, unable to match
        if ($keyExists === false) {
            return false;
        }

        switch ($operator) {
            case self::OPER_EQUAL:
                /** @noinspection TypeUnsafeComparisonInspection */
                return $value == $expected;

            case self::OPER_NOT_EQUAL:
                /** @noinspection TypeUnsafeComparisonInspection */
                return $value != $expected;

            case self::OPER_IDENTICAL:
                return $value === $expected;

            case self::OPER_NOT_IDENTICAL:
                return $value !== $expected;

            case self::OPER_LESS_THAN:
                return $value < $expected;

            case self::OPER_GREATER_THAN:
                return $value > $expected;

            case self::OPER_LESS_OR_EQUAL:
                return $value <= $expected;

            case self::OPER_GREATER_OR_EQUAL:
                return $value >= $expected;

            case self::OPER_PREG:
                if (is_scalar($value) === false) {
                    // Object (DateTime or stdClass) or array not comparable as string
                    return false;
                }
                $result = preg_match($expected, (string)$value);
                if ($result === false) {
                    $error = preg_last_error_msg();
                    throw new LogicException("Invalid PREG format: {$error}", preg_last_error());
                }
                return (bool)$result;

            default:
                throw new LogicException("Filter operator '$operator' is invalid");
        }
    }
}
