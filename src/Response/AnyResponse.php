<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Response;

class AnyResponse implements IAnyResponseProvider
{
    /** @var array */
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function isOkStatus(): bool
    {
        return $this->response['status'] === 'ok';
    }

    public function isErrorStatus(): bool
    {
        return $this->response['status'] === 'error';
    }

    public function getStatus(): ?string
    {
        return $this->response['status'] ?? null;
    }

    public function getData(): array
    {
        return $this->response['data'] ?? [];
    }

    public function getError(): array
    {
        return $this->response['error'] ?? [];
    }

    public function getResponse(): ?AnyResponse
    {
        return $this;
    }
}
