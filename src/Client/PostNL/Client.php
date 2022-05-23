<?php

declare(strict_types=1);

namespace Setono\SyliusPickupPointPlugin\Client\PostNL;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Safe\Exceptions\JsonException;
use Setono\SyliusPickupPointPlugin\Client\ClientInterface;
use Setono\SyliusPickupPointPlugin\Exception\RequestFailedException;
use Setono\SyliusPickupPointPlugin\Model\Query\ServicePointQueryInterface;
use function Safe\json_decode;
use function Safe\json_encode;
use const PHP_QUERY_RFC3986;

final class Client implements ClientInterface
{
    private HttpClientInterface $httpClient;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    private string $baseUrl;

    private string $apiKey;

    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $apiKey,
        string $baseUrl = 'https://api-sandbox.postnl.nl'
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    private function get(string $endpoint, array $params = []): array
    {
        return $this->sendRequest('GET', $endpoint, $params);
    }

    /**
     * @throws ClientExceptionInterface|JsonException
     */
    private function sendRequest(string $method, string $endpoint, array $params = [], array $body = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/') . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        $request = $this->requestFactory->createRequest($method, $url);

        if (count($body) > 0) {
            $request = $request->withBody($this->streamFactory->createStream(json_encode($body)));
        }

        $request = $request->withHeader('apikey', $this->apiKey);
        $request = $request->withHeader('accept', 'application/json');

        $response = $this->httpClient->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            throw new RequestFailedException($request, $response, $response->getStatusCode());
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (!isset($data['GetLocationsResult']['ResponseLocation']))
        {
            throw new \UnexpectedValueException(
                "Expected field '['GetLocationsResult']['ResponseLocation']' to be set."
            );
        }

        return $data['GetLocationsResult']['ResponseLocation'];
    }

    /**
     * @throws ClientExceptionInterface|JsonException
     */
    public function locate(ServicePointQueryInterface $servicePointQuery): iterable
    {
        return $this->get($servicePointQuery->getEndPoint(), $servicePointQuery->toArray());
    }
}
