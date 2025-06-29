<?php
namespace App\Doctrine\Type;

use Doctrine\DBAL\Types\Type;
use App\Exception\BusinessException;
use App\ValueObject\Product\ProductFormat;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ProductFormatType extends Type
{
    public const NAME = 'product_format';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // Definimos longitud máxima para los formatos (ejemplo: "LP+12''" son 6 caracteres)
        return $platform->getVarcharTypeDeclarationSQL([
            'length' => 10,
            'fixed' => false,
        ]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProductFormat
    {
        if ($value === null || $value instanceof ProductFormat) {
            return $value;
        }

        try {
            return new ProductFormat($value);
        } catch (BusinessException $e) {
            throw new \Doctrine\DBAL\Types\ConversionException(
                sprintf('Error al convertir "%s" a ProductFormat: %s', $value, $e->getMessage())
            );
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ProductFormat) {
            return $value->getValue();
        }

        throw new BusinessException('Se esperaba un objeto ProductFormat.');
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