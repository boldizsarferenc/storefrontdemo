<?php

namespace App\Domain\Catalog;

interface CatalogAdapter
{
    /**
     * @param string $sku
     * @param int $quantity
     * @return void
     * @throw StockNotEnoughException
     * @throw CatalogAdapterException
     */
    public function addStock(string $sku, int $quantity): void;

    /**
     * @param string $sku
     * @param int $quantity
     * @return void
     * @throw StockNotEnoughException
     */
    public function subtractStock(string $sku, int $quantity): void;
}
