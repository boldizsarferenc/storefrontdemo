<?php

namespace App\Infrastructure\Adapter\Catalog;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CatalogHTTPClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $resource
     * @param string $productId
     * @param int $quantity
     * @return int
     * @throws GuzzleException
     */
    public function postStock(string $resource, string $productId, int $quantity): int
    {
        $uri = 'http://api_gateway_nginx:8080/catalog/api/products/'. $resource . '/' . $productId . '?quantity=' . $quantity;
        $options = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
        ];

        $response = $this->client->post($uri, $options);

        return $response->getStatusCode();
    }

    /**
     * @param string $sku
     * @return array|null
     * @throws GuzzleException
     */
    public function getProductBySku(string $sku): array|null
    {
        $uri = 'http://api_gateway_nginx:8080/catalog/api/products/by_sku/' . $sku;
        $options = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
        ];

        $response = $this->client->get($uri, $options);

        return json_decode($response->getBody(), true);
    }
}
