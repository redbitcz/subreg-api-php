<?php

declare(strict_types=1);

namespace Soukicz\SubregApi;

use Soukicz\SubregApi\Exception\LogicException;

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
}
