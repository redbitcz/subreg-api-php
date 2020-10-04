<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Response;

use Redbitcz\SubregApi\Exception\LogicException;

class ErrorResponse implements IAnyResponseProvider
{
    private const ERROR_CODE_DELIM = ':';

    /** @var string */
    private $message;
    /** @var string */
    private $majorCode;
    /** @var string */
    private $minorCode;
    /** @var AnyResponse|null */
    private $response;

    public function __construct(string $message, string $majorCode, string $minorCode, ?AnyResponse $response = null)
    {
        $this->message = $message;
        $this->majorCode = $majorCode;
        $this->minorCode = $minorCode;
        $this->response = $response;
    }

    public static function fromAnyResponse(AnyResponse $response): self
    {
        if ($response->isErrorStatus() === false) {
            throw new LogicException(sprintf('Unable to create \'%s\' from non error state Response', __CLASS__));
        }
        $error = $response->getError();
        return new self(
            $error['errormsg'] ?? 'Unknown error (missing \'errormsg\' field)',
            $error['errorcode']['major'] ?? "0",
            $error['errorcode']['minor'] ?? "0",
            $response
        );
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): string
    {
        return $this->majorCode . self::ERROR_CODE_DELIM . $this->minorCode;
    }

    public function getMajorCode(): string
    {
        return $this->majorCode;
    }

    public function getMajorCodeValue(): int
    {
        return (int)$this->majorCode;
    }

    public function getMinorCode(): string
    {
        return $this->minorCode;
    }

    public function getMinorCodeValue(): int
    {
        return (int)$this->minorCode;
    }

    public function getResponse(): ?AnyResponse
    {
        return $this->response;
    }
}
