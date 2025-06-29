<?php
namespace App\DTO\Product;

final class ProductTagDto
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name
    ) {} 
}