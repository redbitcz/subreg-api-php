<?php

declare(strict_types=1);

namespace Soukicz\SubregApi;

use Closure;
use DateTime;
use SoapClient;
use SoapFault;
use Soukicz\SubregApi\Exception\AccessDeniedException;
use Soukicz\SubregApi\Exception\ConnectionException;
use Soukicz\SubregApi\Exception\InvalidArgumentException;
use Soukicz\SubregApi\Exception\InvalidCredentialsException;
use Soukicz\SubregApi\Exception\InvalidRequestException;
use Soukicz\SubregApi\Exception\NotFoundException;
use Soukicz\SubregApi\Exception\ResponseErrorException;
use Soukicz\SubregApi\Exception\UnauthorizedException;
use Soukicz\SubregApi\Exception\UnexpectedResponseException;
use Soukicz\SubregApi\Response\AnyResponse;
use Soukicz\SubregApi\Response\ErrorResponse;
use Soukicz\SubregApi\Response\Response;
use Soukicz\SubregApi\TokenCache\ITokenCache;
use Soukicz\SubregApi\TokenCache\MemoryCache;

class Client
{
    /** @var SoapClient|null */
    private $client;
    /** @var Credentials */
    private $credentials;
    /** @var ITokenCache */
    private $tokenCache;

    public function __construct(Credentials $credentials, ?ITokenCache $tokenCache = null)
    {
        $this->credentials = $credentials;
        $this->tokenCache = $tokenCache ?? new MemoryCache();
    }

    public function call(string $command, array $data = []): Response
    {
        return $this->processCall($command, $data, $this->getApiToken());
    }

    private function getApiToken(): string
    {
        return $this->tokenCache->load($this->credentials->getIdentityHash(), Closure::fromCallable('self::login'));
    }

    private function login(): string
    {
        $credentialsData = [
            'login' => $this->credentials->getLogin(),
            'password' => $this->credentials->getPassword(),
        ];
        $response = $this->processCall('Login', $credentialsData);

        if ($response->hasField('ssid') === false) {
            throw new UnexpectedResponseException(
                "Expected Login response field 'ssid' missing",
                0,
                $response
            );
        }

        return (string)$response->getField('ssid');
    }

    private function processCall(string $command, array $data, ?string $token = null): Response
    {
        try {
            $client = $this->getClient();
        } catch (SoapFault $e) {
            throw new InvalidRequestException("Unable to create SoapClient: {$e->getMessage()}", $e->getCode(), $e);
        }

        if ($token !== null) {
            $data['ssid'] = $token;
        }

        try {
            $responseContent = $client->__soapCall($command, ['data' => $data]);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (SoapFault $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
        $response = new AnyResponse($responseContent);

        if ($response->isErrorStatus()) {
            $errorResponse = ErrorResponse::fromAnyResponse($response);
            throw $this->createResponseErrorException($errorResponse);
        }

        if ($response->isOkStatus()) {
            return Response::fromAnyResponse($response);
        }

        throw new UnexpectedResponseException(
            "Excpected response status 'ok' or 'error'; '{$response->getStatus()}' response status instead",
            0,
            $response
        );
    }


    /**
     * @return SoapClient
     * @throws SoapFault
     */
    private function getClient(): SoapClient
    {
        if ($this->client === null) {
            $this->client = new SoapClient(
                null,
                [
                    'location' => $this->credentials->getUrl(),
                    'uri' => $this->credentials->getNamespace()
                ]
            );
        }

        return $this->client;
    }

    /**
     * @link https://subreg.cz/manual/?cmd=Error_Codes
     * @param ErrorResponse $response
     * @return ResponseErrorException
     */
    private function createResponseErrorException(ErrorResponse $response): ResponseErrorException
    {
        $map = [
            '500:101' => UnauthorizedException::class,
            '500:102' => InvalidArgumentException::class,
            '500:103' => InvalidArgumentException::class,
            '500:104' => InvalidCredentialsException::class,
            '500:105' => AccessDeniedException::class,

            '501:1001' => InvalidArgumentException::class,
            '501:1003' => InvalidArgumentException::class,
            '501:1004' => NotFoundException::class,
            '501:1005' => InvalidArgumentException::class,

            '503:1001' => InvalidArgumentException::class,
            '503:1002' => InvalidArgumentException::class,
            '503:1003' => InvalidArgumentException::class,
            '503:1004' => NotFoundException::class,
            '503:1005' => InvalidArgumentException::class,
        ];
        $exceptionClass = $map[$response->getCode()] ?? ResponseErrorException::class;

        return new $exceptionClass($response);
    }
}
