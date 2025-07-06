<?php
namespace App\Tests\Entity\Product;

use App\Entity\ProductImage;
use App\Entity\ProductEdition;
use App\Entity\ProductUsedItem;
use PHPUnit\Framework\TestCase;

class ProductImageTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $filename = 'image.jpg';
        $path = '/images/product/';
        $productImage = new ProductImage($filename, $path);

        $this->assertSame($filename, $productImage->getFilename());
        $this->assertSame($path, $productImage->getPath());
        $this->assertInstanceOf(\DateTimeInterface::class, $productImage->getCreatedAt());
    }

    public function testSettersAndGetters()
    {
        $productImage = new ProductImage('foo.jpg', '/foo/');

        // Test filename
        $productImage->setFilename('bar.jpg');
        $this->assertSame('bar.jpg', $productImage->getFilename());

        // Test path
        $productImage->setPath('/bar/');
        $this->assertSame('/bar/', $productImage->getPath());

        // Test createdAt
        $date = new \DateTime('2020-01-01');
        $productImage->setCreatedAt($date);
        $this->assertSame($date, $productImage->getCreatedAt());

        // Test id setter/getter
        $productImage->setId(123);
        $this->assertSame(123, $productImage->getId());
    }

    public function testProductEditionAssociation()
    {
        $productImage = new ProductImage('img.jpg', '/img/');
        $productEdition = $this->createMock(ProductEdition::class);

        // Initially null
        $this->assertNull($productImage->getProductEdition());

        // Set and get ProductEdition
        $productImage->setProductEdition($productEdition);
        $this->assertSame($productEdition, $productImage->getProductEdition());

        // Set null allowed?
        $productImage->setProductEdition(null);
        $this->assertNull($productImage->getProductEdition());
    }

    public function testProductUsedItemAssociation()
    {
        $productImage = new ProductImage('img.jpg', '/img/');
        $productUsedItem = $this->createMock(ProductUsedItem::class);

        // Initially null
        $this->assertNull($productImage->getProductUsedItem());

        // Set and get ProductUsedItem
        $productImage->setProductUsedItem($productUsedItem);
        $this->assertSame($productUsedItem, $productImage->getProductUsedItem());
    }
}