<?php
namespace App\DTO\Product;

final class ProductTagFilterDto
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 10
    ) {}  
}