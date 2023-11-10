<?php

namespace App\Infrastructure\Adapter\CatalogApi;

use App\Domain\Catalog\CatalogAdapter as CatalogAdapterInterface;

class CatalogAdapter implements CatalogAdapterInterface
{
    private CatalogHTTPClient $client;

    public function __construct(CatalogHTTPClient $client)
    {
        $this->client = $client;
    }

    public function addStock(string $sku, int $quantity): void
    {
        $product = $this->client->getProductBySku($sku);
        $this->client->postStock('add_stock', $product['id'], $quantity);
    }

    public function subtractStock(string $sku, int $quantity): bool
    {
        $product = $this->client->getProductBySku($sku);
        $response = $this->client->postStock('subtract_stock', $product['id'], $quantity);

        return $response === 200;
    }

    public function getBySku(string $sku): array
    {
        return $this->client->getProductBySku($sku);
    }
}
