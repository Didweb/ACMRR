<?php
namespace App\Service;

use Picqer\Barcode\BarcodeGeneratorPNG;
use App\ValueObject\Product\ProductBarcode;

class BarcodeImageGenerator
{

    public function __construct(private BarcodeGeneratorPNG $generator)
    {}

    public function generate(ProductBarcode $barcode, int $scale = 2, int $height = 30): string
    {
        return $this->generator->getBarcode(
            $barcode->value(),
            $this->generator::TYPE_EAN_13,
            $scale,
            $height
        );
    }

    public function generateBase64(ProductBarcode $barcode, int $scale = 2, int $height = 30): string
    {
        $png = $this->generate($barcode, $scale, $height);
        return 'data:image/png;base64,' . base64_encode($png);
    }
}