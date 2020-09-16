<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Exception;

use Soukicz\SubregApi\Response\ErrorResponse;
use Throwable;

class ResponseErrorException extends NetworkException
{
    /** @var ErrorResponse */
    private $response;
    /** @var string */
    protected $code;

    public function __construct(ErrorResponse $response, ?Throwable $previous = null)
    {
        parent::__construct($response->getMessage(), $response->getMajorCodeValue(), $previous);
        $this->response = $response;
        $this->code = $response->getCode();
    }

    public function getResponse(): ErrorResponse
    {
        return $this->response;
    }

    public function getMajorCode(): int
    {
        return $this->response->getMajorCodeValue();
    }

    public function getMinorCode(): int
    {
        return $this->response->getMinorCodeValue();
    }
}
