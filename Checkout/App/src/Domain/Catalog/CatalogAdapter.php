<?php

namespace App\Domain\Catalog;

interface CatalogAdapter
{
    public function addStock(string $sku, int $quantity): void;

    public function subtractStock(string $sku, int $quantity): void;
}
