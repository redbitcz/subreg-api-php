<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi;

use Closure;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Redbitcz\SubregApi\Exception\ConnectionException;
use Redbitcz\SubregApi\Exception\InvalidRequestException;
use Redbitcz\SubregApi\Exception\UnauthorizedException;
use Redbitcz\SubregApi\Exception\UnexpectedResponseException;
use Redbitcz\SubregApi\Response\AnyResponse;
use Redbitcz\SubregApi\Response\ErrorResponse;
use Redbitcz\SubregApi\Response\Response;
use Redbitcz\SubregApi\Response\ResponseExceptionMapper;
use Redbitcz\SubregApi\TokenCache\ITokenCache;
use Redbitcz\SubregApi\TokenCache\MemoryCache;
use SoapClient;
use SoapFault;

class Client
{
    use LoggerAwareTrait;

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
        $this->setLogger(new NullLogger());
    }

    public function call(string $command, array $data = []): Response
    {
        if (strcasecmp(trim($command), 'Login') === 0) {
            throw new InvalidRequestException(
                'Do not call \'Login\' command directly - package is fully handling authentication for You'
            );
        }

        $apiToken = $this->getApiToken();
        $this->logger->debug(__CLASS__ . ' ' . $command, $data);
        try {
            return $this->processCall($command, $data, $apiToken);
        } catch (UnauthorizedException $e) {
            // try again with fresh token
            $apiToken = $this->reloadApiToken();
            return $this->processCall($command, $data, $apiToken);
        }
    }

    private function getApiToken(): string
    {
        return $this->tokenCache->load($this->credentials->getIdentityHash(), Closure::fromCallable('self::login'));
    }

    protected function reloadApiToken(): string
    {
        $this->tokenCache->clear($this->credentials->getIdentityHash());
        return $this->getApiToken();
    }

    private function login(): string
    {
        $credentialsData = [
            'login' => $this->credentials->getLogin(),
            'password' => $this->credentials->getPassword(),
        ];
        $this->logger->debug(__CLASS__ . ' Login', ['password' => '**REDACTED**'] + $credentialsData);
        $response = $this->processCall('Login', $credentialsData);

        if ($response->hasItem('ssid') === false) {
            throw new UnexpectedResponseException(
                "Expected Login response field 'ssid' missing",
                0,
                $response
            );
        }

        return (string)$response->getItem('ssid');
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
            throw ResponseExceptionMapper::createExcetionForResponse($errorResponse);
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
}
