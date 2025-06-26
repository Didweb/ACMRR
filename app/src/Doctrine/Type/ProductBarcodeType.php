<?php
namespace App\Doctrine\Type;

use App\Exception\BusinessException;
use Doctrine\DBAL\Types\Type;
use App\ValueObject\Product\ProductBarcode;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ProductBarcodeType extends Type
{
    public const NAME = 'product_barcode';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL([
            'length' => 13,
            'fixed' => true,
        ]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProductBarcode
    {
        if ($value === null || $value instanceof ProductBarcode) {
            return $value;
        }

        return new ProductBarcode($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ProductBarcode) {
            return $value->value();
        }

        throw new BusinessException('Expected ProductBarcode object.');
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}