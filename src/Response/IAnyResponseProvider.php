<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Response;

interface IAnyResponseProvider
{
    public function getResponse(): ?AnyResponse;
}
