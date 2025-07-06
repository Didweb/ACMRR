<?php
namespace App\Tests\ValueObject\Product;

use PHPUnit\Framework\TestCase;
use App\Exception\BusinessException;
use App\ValueObject\Product\ProductFormat;

class ProductFormatTest extends TestCase
{
    public function testValidFormatsDoNotThrow(): void
    {
        $validFormats = ["7''", "ep 12''", "lp", "LP+12''", "lp+7''"];

        foreach ($validFormats as $format) {
            $pf = new ProductFormat($format);
            $this->assertInstanceOf(ProductFormat::class, $pf);
            // Debe guardar en mayúsculas
            $this->assertSame(strtoupper($format), $pf->getValue());
        }
    }

    public function testInvalidFormatThrowsException(): void
    {
        $this->expectException(BusinessException::class);

        new ProductFormat('INVALID');
    }

    public function testToStringReturnsValue(): void
    {
        $pf = new ProductFormat("lp");
        $stringPf = $pf->__toString();
        $this->assertSame("LP", $stringPf);
        $this->assertIsString($stringPf);
    }

    public function testEqualsMethod(): void
    {
        $pf1 = new ProductFormat("lp");
        $pf2 = new ProductFormat("LP");
        $pf3 = new ProductFormat("7''");

        $this->assertTrue($pf1->equals($pf2));
        $this->assertFalse($pf1->equals($pf3));
    }


    public function testChoicesReturnProductFormatInstances(): void
    {
        $choices = ProductFormat::choices();

        $this->assertIsArray($choices);
        $this->assertNotEmpty($choices);
        foreach ($choices as $choice) {
            $this->assertInstanceOf(ProductFormat::class, $choice);
        }
    }

    public function testChoicesStrReturnValidFormatsArray(): void
    {
        $choicesStr = ProductFormat::choicesStr();

        $this->assertIsArray($choicesStr);
        $this->assertContains("LP", $choicesStr);
        $this->assertContains("7''", $choicesStr);
    }
}