<?php
namespace App\ValueObject\Product;

use App\Exception\BusinessException;

final class ProductBarcode
{
    private string $value;
    
    public function __construct(string $value)
    {
        $value = preg_replace('/[^0-9]/', '', $value); // Sanitizar
        if (!self::isValidEAN13($value)) {
            throw new BusinessException("Invalid EAN-13 barcode: $value");
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ProductBarcode $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function generate(string $prefix = '230'): self
    {
        $randomLength = 12 - strlen($prefix);
         if ($randomLength <= 0) {
            throw new BusinessException('Prefix too long for EAN-13. Must be max 12 digits.');
        }
        
        $randomPart = str_pad((string)random_int(0, (10 ** $randomLength) - 1), $randomLength, '0', STR_PAD_LEFT);
        $base = $prefix . $randomPart;

        $checkDigit = self::calculateCheckDigit($base);
        return new self($base . $checkDigit);
    }

    private static function isValidEAN13(string $ean): bool
    {
        if (strlen($ean) !== 13 || !ctype_digit($ean)) {
            return false;
        }

        return (int)substr($ean, -1) === self::calculateCheckDigit(substr($ean, 0, 12));
    }

    private static function calculateCheckDigit(string $digits): int
    {
        if (strlen($digits) !== 12 || !ctype_digit($digits)) {
            throw new BusinessException("EAN-13 must have 12 digits before calculating check digit.");
        }

        $sum = 0;
        foreach (str_split($digits) as $i => $d) {
            $sum += (int)$d * ($i % 2 === 0 ? 1 : 3);
        }

        $mod = $sum % 10;
        return $mod === 0 ? 0 : 10 - $mod;
    }
}