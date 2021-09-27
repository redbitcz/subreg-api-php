<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Schema;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Nette\Schema\Context;
use Nette\Schema\Message;
use Nette\Schema\Schema;

class DateTime implements Schema
{
    /** @var bool */
    private $required = false;

    /** @var bool */
    private $nullable = true;

    /** @var string */
    private $format;

    /** @var DateTimeZone */
    private $timeZone;

    public function __construct($format = 'Y-m-d H:i:s', ?DateTimeZone $timeZone = null)
    {
        $this->format = $format;
        $this->timeZone = $timeZone ?? new DateTimeZone(date_default_timezone_get());
    }

    public function required(bool $state = true): self
    {
        $this->required = $state;
        return $this;
    }

    public function nullable(bool $state = true): self
    {
        $this->nullable = $state;
        return $this;
    }

    /**
     * @param DateTimeInterface|string|null $value
     */
    public function normalize($value, Context $context): ?DateTimeImmutable
    {
        // Must be Date, string or empty (null / 0 / false / "")
        if ($value instanceof DateTimeInterface === false && is_string($value) === false && empty($value) === false) {
            $type = gettype($value);
            $context->addError(
                "The option %path% expects Date, $type given.",
                Message::TYPE_MISMATCH
            );
            return null;
        }

        if (empty($value)) {
            if ($this->nullable === false) {
                $context->addError(
                    "The option %path% expects not-nullable Date, empty value given.",
                    Message::FAILED_ASSERTION
                );
            }

            return null;
        }

        // Translate format
        if (is_string($value)) {
            $normalized = DateTimeImmutable::createFromFormat($this->format, $value, $this->timeZone);

            if ($normalized instanceof DateTimeImmutable === false) {
                $context->addError(
                    "The option %path% expects Date to match pattern '$this->format', '$value' given.",
                    Message::PATTERN_MISMATCH
                );

                return null;
            }
        } elseif ($value instanceof \DateTime) {
            $normalized = DateTimeImmutable::createFromMutable($value);
        } else {
            $normalized = $value;
        }

        return $normalized;
    }

    public function merge($value, $base)
    {
        return $value;
    }

    public function complete($value, Context $context)
    {
        return $value;
    }

    public function completeDefault(Context $context)
    {
        if ($this->required) {
            $context->addError('The mandatory option %path% is missing.', Message::MISSING_ITEM);
        }
        return null;
    }
}
