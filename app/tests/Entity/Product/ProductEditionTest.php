<?php
namespace App\Tests\Entity\Product;

use App\Entity\Track;
use App\Entity\Artist;
use App\Entity\ProductTag;
use App\Entity\RecordLabel;
use App\Entity\ProductImage;
use App\Entity\ProductTitle;
use App\Entity\ProductEdition;
use App\Entity\ProductUsedItem;
use PHPUnit\Framework\TestCase;
use App\ValueObject\Product\ProductFormat;
use App\ValueObject\Product\ProductBarcode;

class ProductEditionTest extends TestCase
{
    private ProductEdition $productEdition;
    private ProductTitle $title;
    private RecordLabel $label;
    private ProductFormat $format;

    protected function setUp(): void
    {
        $this->title = new ProductTitle();
        $this->title->setId(1);
        $this->title->setName('Test Title'); 

        $this->label = new RecordLabel();
        $this->label->setId(1); 
        $this->label->setName('Test Label'); 

        $this->format = new ProductFormat('LP');

        $this->productEdition = new ProductEdition();
        $this->productEdition->setTitle($this->title);
        $this->productEdition->setLabel($this->label);
        $this->productEdition->setFormat($this->format);
        $this->productEdition->setPriceNew(29.99);
        $this->productEdition->setStockNew(100);
        $this->productEdition->setYear(2000);
    }

    public function testGettersAndSetters(): void
    {
        $this->assertSame($this->title, $this->productEdition->getTitle());
        $this->assertSame($this->label, $this->productEdition->getLabel());
        $this->assertSame($this->format, $this->productEdition->getFormat());
        $this->assertEquals(29.99, $this->productEdition->getPriceNew());
        $this->assertEquals(100, $this->productEdition->getStockNew());

        $this->productEdition->setYear(2020);
        $this->assertEquals(2020, $this->productEdition->getYear());

        $barcode = new ProductBarcode('4006381333931');
        $this->productEdition->setBarcode($barcode);
        $this->assertSame($barcode, $this->productEdition->getBarcode());
    }

    public function testAddAndRemoveProductUsedItem(): void
    {
        $item = new ProductUsedItem();
        $item->setEdition($this->productEdition);

        $this->productEdition->addProductUsedItem($item);
        $this->assertTrue($this->productEdition->getProductUsedItems()->contains($item));
        $this->assertSame($this->productEdition, $item->getEdition());

        $this->productEdition->removeProductUsedItem($item);
        $this->assertFalse($this->productEdition->getProductUsedItems()->contains($item));
        $this->assertNull($item->getEdition());
    }

    public function testAddAndRemoveArtist(): void
    {
        $artist = new Artist();

        $this->productEdition->addArtist($artist);
        $this->assertTrue($this->productEdition->getArtists()->contains($artist));
        $this->assertTrue($artist->getProductEditions()->contains($this->productEdition));

        $this->productEdition->removeArtist($artist);
        $this->assertFalse($this->productEdition->getArtists()->contains($artist));
        $this->assertFalse($artist->getProductEditions()->contains($this->productEdition));
    }

    public function testAddAndRemoveTrack(): void
    {
        $track = new Track();

        $this->productEdition->addTrack($track);
        $this->assertTrue($this->productEdition->getTracks()->contains($track));
        $this->assertSame($this->productEdition, $track->getProductEdition());

        $this->productEdition->removeTrack($track);
        $this->assertFalse($this->productEdition->getTracks()->contains($track));

        $this->assertNull($track->getProductEdition());
    }

    public function testAddAndRemoveImage(): void
    {
        $image = new ProductImage('test.jpg', '../../Service/Product/fixture/example.jpg');

        $this->productEdition->addImage($image);
        $this->assertTrue($this->productEdition->getImages()->contains($image));
        $this->assertSame($this->productEdition, $image->getProductEdition());

        $this->productEdition->removeImage($image);
        $this->assertFalse($this->productEdition->getImages()->contains($image));
        $this->assertNull($image->getProductEdition());
    }


    public function testAddAndRemoveTag(): void
    {
        $tag = new ProductTag();

        $this->productEdition->addTag($tag);
        $this->assertTrue($this->productEdition->getTags()->contains($tag));
        $this->assertTrue($tag->getProductEditions()->contains($this->productEdition));

        $this->productEdition->removeTag($tag);
        $this->assertFalse($this->productEdition->getTags()->contains($tag));
        $this->assertFalse($tag->getProductEditions()->contains($this->productEdition));
    }

    public function testToArray(): void
    {
       
        $array = $this->productEdition->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('year', $array);
        $this->assertArrayHasKey('format', $array);
        $this->assertArrayHasKey('barcode', $array);
        $this->assertArrayHasKey('stockNew', $array);
        $this->assertArrayHasKey('priceNew', $array);
        $this->assertArrayHasKey('tags', $array);
        $this->assertArrayHasKey('images', $array);
        $this->assertArrayHasKey('productUsedItems', $array);
        $this->assertArrayHasKey('artists', $array);
        $this->assertArrayHasKey('tracks', $array);
    }

    public function testGetImagesArray(): void
    {
        $image1 = new ProductImage('cover.jpg', '/uploads/products/cover.jpg');
        $image2 = new ProductImage('back.jpg', '/uploads/products/back.jpg');
        $image1->setId(1);
        $image2->setId(2);

        $this->productEdition->addImage($image1);
        $this->productEdition->addImage($image2);

        $expected = [
            [
                'id' => 1,
                'filename' => 'cover.jpg',
                'url' => '/uploads/products/cover.jpg',
            ],
            [
                'id' => 2,
                'filename' => 'back.jpg',
                'url' => '/uploads/products/back.jpg',
            ],
        ];

        $result = $this->productEdition->getImagesArray();

        $this->assertEquals($expected, $result);
    }
}