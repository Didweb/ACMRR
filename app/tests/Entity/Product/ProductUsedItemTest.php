<?php
namespace App\Tests\Entity\Product;

use App\Entity\ProductImage;
use App\Entity\ProductEdition;
use App\Entity\ProductUsedItem;
use PHPUnit\Framework\TestCase;
use App\ValueObject\Product\ProductStatus;
use App\ValueObject\Product\ProductBarcode;
use Doctrine\Common\Collections\Collection;

class ProductUsedItemTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $usedItem = new ProductUsedItem();

        $usedItem->setId(42);
        $this->assertSame(42, $usedItem->getId());

        $edition = new ProductEdition();
        $usedItem->setEdition($edition);
        $this->assertSame($edition, $usedItem->getEdition());

        $barcode = new ProductBarcode('4006381333931');
        $usedItem->setBarcode($barcode);
        $this->assertSame($barcode, $usedItem->getBarcode());

        $conditionVinyl = new ProductStatus('G');
        $usedItem->setConditionVinyl($conditionVinyl);
        $this->assertSame($conditionVinyl, $usedItem->getConditionVinyl());

        $conditionFolder = new ProductStatus('VG');
        $usedItem->setConditionFolder($conditionFolder);
        $this->assertSame($conditionFolder, $usedItem->getConditionFolder());

        $usedItem->setPrice(19.99);
        $this->assertSame(19.99, $usedItem->getPrice());
    }

    public function testImagesCollectionInitiallyEmpty()
    {
        $usedItem = new ProductUsedItem();
        $this->assertInstanceOf(Collection::class, $usedItem->getImages());
        $this->assertCount(0, $usedItem->getImages());
    }

    public function testAddImage()
    {
        $usedItem = new ProductUsedItem();
        $image = new ProductImage('filename.jpg', '/path/to/file');

        $usedItem->addImage($image);

        $images = $usedItem->getImages();
        $this->assertCount(1, $images);
        $this->assertTrue($images->contains($image));
        // El método addImage no está seteando la relación inversa correctamente
        // porque usa $image->getProductUsedItem($this); que parece error
        // Se debería llamar a $image->setProductUsedItem($this);
        // No podemos testear esa parte bien sin modificar la entidad
    }

    public function testRemoveImage()
    {
        $usedItem = new ProductUsedItem();
        $image = new ProductImage('filename.jpg', '/path/to/file');

        $usedItem->addImage($image);
        $this->assertCount(1, $usedItem->getImages());

        $usedItem->removeImage($image);
        $this->assertCount(0, $usedItem->getImages());
        // Igual que antes, el método removeImage llama a getProductUsedItem(null)
        // que parece error, debería ser setProductUsedItem(null)
        // Por ahora no podemos testear la limpieza de la relación inversa
    }

    public function testToArray()
    {
        $usedItem = new ProductUsedItem();

        $usedItem->setId(7);
        $barcode = new ProductBarcode('4006381333931');
        $usedItem->setBarcode($barcode);

        $conditionVinyl = new ProductStatus('VG');
        $conditionFolder = new ProductStatus('VG');
        $usedItem->setConditionVinyl($conditionVinyl);
        $usedItem->setConditionFolder($conditionFolder);

        $usedItem->setPrice(49.95);

        // La colección está vacía
        $array = $usedItem->toArray();

        $this->assertSame(7, $array['id']);
        $this->assertSame('4006381333931', $array['barcode']);
        $this->assertSame('VG', $array['conditionVinyl']);
        $this->assertSame('VG', $array['conditionFolder']);
        $this->assertSame(49.95, $array['price']);
        $this->assertCount(0, $array['images']);
    }
}