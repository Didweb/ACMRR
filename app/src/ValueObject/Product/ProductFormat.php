<?php
namespace App\ValueObject\Product;

use App\Exception\BusinessException;

class ProductFormat
{
    private const VALID_FORMATS = ["7''", "EP 12''", "LP", "LP+12''", "LP+7''"]; 

    private string $value;

    public function __construct(string $value)
    {
        $value = strtoupper($value);

        if (!in_array($value, self::VALID_FORMATS, true)) {
            throw new BusinessException(sprintf('Formato de producto inválido: %s', $value));
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(ProductFormat $other): bool
    {
        return $this->value === $other->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function choices(): array
    {
        return array_map(fn(string $format) => new self($format), self::VALID_FORMATS);
    }

    public static function choicesStr(): array
    {
        return self::VALID_FORMATS;
    }
}