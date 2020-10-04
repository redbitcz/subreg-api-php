<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Schema;

use DateTimeImmutable;
use DateTimeZone;
use Nette\Schema\Context;
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

    public function normalize($value, Context $context): ?DateTimeImmutable
    {
        // Must be string or empty (null / 0 / false /
        if (is_string($value) === false && empty($value) === false) {
            $type = gettype($value);
            $context->addError("The option %path% expects Date, $type given.");
            return null;
        }
        if ($this->nullable === false && empty($value)) {
            $context->addError("The option %path% expects not-nullable Date, nothing given.");
            return null;
        }

        $normalized = DateTimeImmutable::createFromFormat($this->format, $value, $this->timeZone);
        if ($normalized instanceof DateTimeImmutable === false) {
            $context->addError("The option %path% expects Date to match pattern '$this->format', '$value' given.");
            return null;
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
            $context->addError('The mandatory option %path% is missing.');
        }
        return null;
    }
}
