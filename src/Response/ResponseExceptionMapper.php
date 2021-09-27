<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Response;

use Redbitcz\SubregApi\Exception as E;

class ResponseExceptionMapper
{
    private const DEFAULT_EXCEPTION_CLASS = E\ResponseErrorException::class;

    private static $map = [
        '500:101' => E\UnauthorizedException::class,
        '500:102' => E\InvalidArgumentException::class,
        '500:103' => E\InvalidArgumentException::class,
        '500:104' => E\InvalidCredentialsException::class,
        '500:105' => E\AccessDeniedException::class,
        '500:106' => E\AccessDeniedException::class,
        '500:108' => E\CommandRateLimitExceeded::class,

        '501:1001' => E\InvalidArgumentException::class,
        '501:1003' => E\InvalidArgumentException::class,
        '501:1004' => E\AccessDeniedException::class,
        '501:1005' => E\InvalidArgumentException::class,
        '501:1006' => E\InvalidArgumentException::class,
        '501:1007' => E\NotFoundException::class,

        '503:1001' => E\InvalidArgumentException::class,
        '503:1002' => E\InvalidArgumentException::class,
        '503:1003' => E\InvalidArgumentException::class,
        '503:1004' => E\NotFoundException::class,
        '503:1005' => E\InvalidArgumentException::class,

        '507:1005' => E\AccessDeniedException::class,

    ];


    public static function getExceptionClassForResponse(ErrorResponse $response): string
    {
        return self::$map[$response->getCode()] ?? self::DEFAULT_EXCEPTION_CLASS;
    }

    public static function createExceptionForResponse(ErrorResponse $response): E\ResponseErrorException
    {
        $exceptionClass = self::getExceptionClassForResponse($response);
        return new $exceptionClass($response);
    }
}
