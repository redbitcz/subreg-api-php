<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Response;

interface IAnyResponseProvider
{
    public function getResponse(): ?AnyResponse;
}
