<?php

namespace App\Infrastructure\Adapter\CatalogApi;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CatalogHTTPClient
{
    public function __construct(private HttpClientInterface $client)
    {
        $this->client = $client->withOptions([
            'base_uri' => 'http://api_gateway_nginx:8080'
        ]);
    }

    public function postStock(string $resource, string $productId, int $quantity): int
    {
        $uri = '/catalog/api/products/'. $resource . '/' . $productId . '?quantity=' . $quantity;

        $response = $this->client->request('POST', $uri);
        return $response->getStatusCode();
    }

    public function getProductBySku(string $sku): array|null
    {
        $uri = '/catalog/api/products/by_sku/' . $sku;

        $response = $this->client->request('GET', $uri);
        return json_decode($response->getContent(), true);
    }
}
