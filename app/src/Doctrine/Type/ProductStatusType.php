<?php
namespace App\Doctrine\Type;

use Doctrine\DBAL\Types\Type;
use App\Exception\BusinessException;
use App\ValueObject\Product\ProductStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ProductStatusType extends Type
{
    public const NAME = 'product_status';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // Tamaño suficiente para los valores posibles (máximo 3 caracteres)
        return $platform->getVarcharTypeDeclarationSQL([
            'length' => 3,
            'fixed' => false,
        ]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProductStatus
    {
        if ($value === null || $value instanceof ProductStatus) {
            return $value;
        }

        try {
            return new ProductStatus($value);
        } catch (BusinessException $e) {
            throw new \Doctrine\DBAL\Types\ConversionException(
                sprintf('Error al convertir "%s" a ProductStatus: %s', $value, $e->getMessage())
            );
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ProductStatus) {
            return $value->getValue();
        }

        throw new BusinessException('Se esperaba un objeto ProductStatus.');
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