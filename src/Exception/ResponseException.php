<?php

declare(strict_types=1);

namespace Soukicz\SubregApi\Exception;

use Soukicz\SubregApi\Response\IAnyResponseProvider;
use Throwable;

class ResponseException extends RuntimeException
{
    /** @var IAnyResponseProvider */
    private $response;

    public function __construct(string $message, int $code, IAnyResponseProvider $response, ?Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): IAnyResponseProvider
    {
        return $this->response;
    }
}
