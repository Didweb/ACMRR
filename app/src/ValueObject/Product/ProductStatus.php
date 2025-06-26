<?php
namespace App\ValueObject\Product;

use App\Exception\BusinessException;
final class ProductStatus
{
   private const VALID_STATUSES = ['NM', 'VG+', 'VG', 'G+', 'G', 'F', 'P']; 

    private string $value;

    public function __construct(string $value)
    {
        $value = strtoupper($value);

        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new BusinessException(sprintf('Estado de producto inválido: %s', $value));
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(ProductStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function getValue(): string
    {
        return $this->value;
    } 
}