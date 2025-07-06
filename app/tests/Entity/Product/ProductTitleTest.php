<?php
namespace App\Tests\Entity\Product;

use App\Entity\ProductTitle;
use App\Entity\ProductEdition;
use PHPUnit\Framework\TestCase;

class ProductTitleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $title = new ProductTitle();

        $title->setId(123);
        $this->assertSame(123, $title->getId());

        $title->setName('My Product Title');
        $this->assertSame('My Product Title', $title->getName());
    }

    public function testEditionsCollectionInitiallyEmpty()
    {
        $title = new ProductTitle();
        $this->assertCount(0, $title->getEditions());
    }

    public function testAddEdition()
    {
        $title = new ProductTitle();
        $edition = new ProductEdition();

        $title->addEdition($edition);

        $this->assertCount(1, $title->getEditions());
        $this->assertTrue($title->getEditions()->contains($edition));
        $this->assertSame($title, $edition->getTitle());

        $title->addEdition($edition);
        $this->assertCount(1, $title->getEditions());
    }

    public function testRemoveEdition()
    {
        $title = new ProductTitle();
        $edition = new ProductEdition();

        $title->addEdition($edition);
        $this->assertCount(1, $title->getEditions());

        $title->removeEdition($edition);
        $this->assertCount(0, $title->getEditions());
        $this->assertNull($edition->getTitle());

        $title->removeEdition($edition);
        $this->assertCount(0, $title->getEditions());
    }
}