<?php
namespace App\Tests\ValueObject\Product;

use PHPUnit\Framework\TestCase;
use App\Exception\BusinessException;
use App\ValueObject\Product\ProductBarcode;

class ProductBarcodeTest extends TestCase
{
    public function testValidBarcodeIsAccepted(): void
    {
        $barcode = new ProductBarcode('4006381333931');
        $this->assertSame('4006381333931', $barcode->value());
    }

    public function testInvalidBarcodeThrowsException(): void
    {
        $this->expectException(BusinessException::class);

        new ProductBarcode('123456789012'); 
    }

    public function testSanitizationRemovesNonDigits(): void
    {
        $barcode = new ProductBarcode(' 40063-8133 3931 ');
        $this->assertSame('4006381333931', $barcode->value());
    }

    public function testToStringReturnsCorrectValue(): void
    {
        $barcode = new ProductBarcode('4006381333931');
        $this->assertSame('4006381333931', (string)$barcode);
    }

    public function testEqualsReturnsTrueForSameValue(): void
    {
        $a = new ProductBarcode('4006381333931');
        $b = new ProductBarcode('4006381333931');
        $this->assertTrue($a->equals($b));
    }

    public function testEqualsReturnsFalseForDifferentValue(): void
    {
        $a = new ProductBarcode('4006381333931');
        $b = ProductBarcode::generate();
        $this->assertFalse($a->equals($b));
    }

    public function testGenerateProducesValidEAN13(): void
    {
        $barcode = ProductBarcode::generate();
        $this->assertSame(13, strlen($barcode->value()));
        $this->assertMatchesRegularExpression('/^\d{13}$/', $barcode->value());
    }

    public function testGenerateWithCustomPrefix(): void
    {
        $barcode = ProductBarcode::generate('999');
        $this->assertStringStartsWith('999', $barcode->value());
        $this->assertSame(13, strlen($barcode->value()));
    }

    public function testGenerateWithTooLongPrefixThrowsException(): void
    {
        $this->expectException(BusinessException::class);

        ProductBarcode::generate('1234567890123');
    }

    public function testCheckDigitIsCorrect(): void
    {
        $base = '400638133393'; // sin check digit
        $expectedCheckDigit = 1;

        $reflection = new \ReflectionClass(ProductBarcode::class);
        $method = $reflection->getMethod('calculateCheckDigit');
        $method->setAccessible(true);

        $checkDigit = $method->invoke(null, $base);
        $this->assertSame($expectedCheckDigit, $checkDigit);
    }
}