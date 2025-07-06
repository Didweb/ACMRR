<?php
namespace App\Tests\ValueObject\Product;

use PHPUnit\Framework\TestCase;
use App\Exception\BusinessException;
use App\ValueObject\Product\ProductStatus;

class ProductStatusTest extends TestCase
{
    public function testValidStatusesDoNotThrow(): void
    {
        $validStatuses = ['NM', 'vg+', 'VG', 'g+', 'G', 'f', 'p'];

        foreach ($validStatuses as $status) {
            $ps = new ProductStatus($status);
            $this->assertInstanceOf(ProductStatus::class, $ps);
            $this->assertSame(strtoupper($status), $ps->getValue());
        }
    }

    public function testInvalidStatusThrowsException(): void
    {
        $this->expectException(BusinessException::class);

        new ProductStatus('INVALID');
    }

    public function testToStringReturnsValue(): void
    {
        $ps = new ProductStatus('vg+');
        $this->assertSame('VG+', (string)$ps);
    }

    public function testEqualsMethod(): void
    {
        $ps1 = new ProductStatus('VG+');
        $ps2 = new ProductStatus('vg+');
        $ps3 = new ProductStatus('NM');

        $this->assertTrue($ps1->equals($ps2));
        $this->assertFalse($ps1->equals($ps3));
    }

    public function testGetValueReturnsCorrectValue(): void
    {
        $ps = new ProductStatus('f');
        $this->assertSame('F', $ps->getValue());
    }
}