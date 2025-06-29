<?php
namespace App\Service\Product;

use Picqer\Barcode\BarcodeGeneratorPNG;
use App\ValueObject\Product\ProductBarcode;

class BarcodeImageGenerator
{
    private BarcodeGeneratorPNG $generator;
    private string $barcodeDir;

    public function __construct(string $barcodeDir)
    {
        $this->generator = new BarcodeGeneratorPNG();
        $this->barcodeDir = rtrim($barcodeDir, '/');
    }

    public function generatePng(ProductBarcode $barcode): string
    {
        return $this->generator->getBarcode($barcode->value(), BarcodeGeneratorPNG::TYPE_EAN_13);
    }

    public function saveBarcodeToFile(ProductBarcode $barcode): string
    {
        $filename = $barcode->value() . '.png';
        $filePath = $this->barcodeDir . '/' . $filename;
        file_put_contents($filePath, $this->generatePng($barcode));
        return $filePath;
    }
}