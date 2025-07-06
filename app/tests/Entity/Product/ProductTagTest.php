<?php
namespace App\Tests\Entity\Product;

use App\Entity\ProductTag;
use App\Entity\ProductEdition;
use PHPUnit\Framework\TestCase;

class ProductTagTest extends TestCase
{
    public function testCanCreateProductTagAndSetName()
    {
        $tag = new ProductTag();
        $tag->setName('Limited Edition');

        $this->assertNull($tag->getId());
        $this->assertSame('Limited Edition', $tag->getName());
        $this->assertCount(0, $tag->getProductEditions());
    }

    public function testAddProductEdition()
    {
        $tag = new ProductTag();

        $edition = new ProductEdition();

        $tag->addProductEdition($edition);

        $this->assertCount(1, $tag->getProductEditions());
        $this->assertTrue($tag->getProductEditions()->contains($edition));
    }

    public function testRemoveProductEdition()
    {
        $tag = new ProductTag();

        $edition = new ProductEdition();

        $tag->getProductEditions()->add($edition);

       
        $tag->removeProductEdition($edition);

        $this->assertCount(0, $tag->getProductEditions());
        $this->assertFalse($tag->getProductEditions()->contains($edition));
    }

    public function testAddSameEditionTwiceDoesNotDuplicate()
    {
        $tag = new ProductTag();

        $edition = new ProductEdition();

        $tag->addProductEdition($edition);
        $tag->addProductEdition($edition); 

        $this->assertCount(1, $tag->getProductEditions());
    }
}