<?php
namespace App\Tests\Service\Product;

use PHPUnit\Framework\TestCase;
use App\ValueObject\Product\ProductBarcode;
use App\Service\Product\BarcodeImageGenerator;

class BarcodeImageGeneratorTest extends TestCase
{
    private string $barcodeDir;

    protected function setUp(): void
    {
        $this->barcodeDir = sys_get_temp_dir() . '/barcodes';
        if (!is_dir($this->barcodeDir)) {
            mkdir($this->barcodeDir, 0777, true);
        }
    }

    public function testGeneratePngReturnsImageString(): void
    {
        $generator = new BarcodeImageGenerator($this->barcodeDir);
        $barcode = ProductBarcode::generate();
        $imageData = $generator->generatePng($barcode);

        $this->assertIsString($imageData);
        $this->assertNotEmpty($imageData);
        $this->assertStringStartsWith("\x89PNG", $imageData);
    }

    public function testSaveBarcodeToFileCreatesFile(): void
    {
        $generator = new BarcodeImageGenerator($this->barcodeDir);
        $barcode = ProductBarcode::generate();
        $filePath = $generator->saveBarcodeToFile($barcode);

        $this->assertFileExists($filePath);
        $this->assertGreaterThan(0, filesize($filePath));
        $this->assertStringEndsWith('.png', $filePath);

        unlink($filePath);
    }
}