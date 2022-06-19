<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Context;

use DateTimeZone;
use Redbitcz\SubregApi\Exception\LogicException;

trait ContextAware
{
    /** @var Context|null */
    private $context;

    protected function setContext(?Context $context): void
    {
        $this->context = $context;
    }

    public function getMandatoryContext(): Context
    {
        if ($this->hasContext() === false) {
            throw new LogicException('Context is not present, unable to use context-based methods');
        }
        return $this->context;
    }

    public function getContext(): ?Context
    {
        return $this->context;
    }

    public function hasContext(): bool
    {
        return $this->context !== null;
    }

    public function getTimeZone(): ?DateTimeZone
    {
        return $this->context !== null ? $this->context->getTimeZone() : null;
    }
}
