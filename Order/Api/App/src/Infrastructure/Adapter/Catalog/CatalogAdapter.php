<?php

namespace App\Infrastructure\Adapter\Catalog;

use App\Domain\Catalog\CatalogAdapter as CatalogAdapterInterface;
use App\Domain\Catalog\CatalogAdapterException;
use App\Domain\Catalog\StockNotEnoughException;
use Psr\Log\LoggerInterface;
use Throwable;

class CatalogAdapter implements CatalogAdapterInterface
{

    private CatalogHTTPClient $client;

    public function __construct(CatalogHTTPClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
    }

    /**
     * @param string $sku
     * @param int $quantity
     * @return void
     * @throws CatalogAdapterException
     */
    public function addStock(string $sku, int $quantity): void
    {
        try {
            $product = $this->client->getProductBySku($sku);
            $response = $this->client->postStock('add_stock', $product['id'], $quantity);
        } catch (Throwable $throwable) {
            throw new CatalogAdapterException('There was an error while adding stock', 0, $throwable);
        }
    }

    /**
     * @param string $sku
     * @param int $quantity
     * @return void
     * @throws CatalogAdapterException
     * @throws StockNotEnoughException
     */
    public function subtractStock(string $sku, int $quantity): void
    {
        try {
            $product = $this->client->getProductBySku($sku);
            $response = $this->client->postStock('subtract_stock', $product['id'], $quantity);

        } catch (Throwable $throwable) {
            throw new CatalogAdapterException('There was an error while subtracting stock', 0, $throwable);
        }

        if($response === 409) {
            throw new StockNotEnoughException('There is not enough stock to subtract. productId: ' . $productId . ' quantity: ' . $quantity);
        }
    }
}
