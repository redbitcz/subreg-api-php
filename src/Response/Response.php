<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Response;

use Redbitcz\SubregApi\Exception\InvalidResponseException;
use Redbitcz\SubregApi\Exception\LogicException;

class Response implements IAnyResponseProvider
{
    /** @var array */
    private $data;
    /** @var AnyResponse|null */
    private $response;

    public function __construct(array $data, ?AnyResponse $response = null)
    {
        $this->data = $data;
        $this->response = $response;
    }

    public static function fromAnyResponse(AnyResponse $response): self
    {
        if ($response->isOkStatus() === false) {
            throw new LogicException(sprintf('Unable to create \'%s\' from non OK state Response', __CLASS__));
        }
        return new self($response->getData(), $response);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getMandatoryField(string $key)
    {
        if ($this->hasField($key) === false) {
            throw new InvalidResponseException("Required response field '{$key}' missing", 0, $this);
        }

        return $this->data[$key];
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getField(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function hasField(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function getResponse(): ?AnyResponse
    {
        return $this->response;
    }
}
