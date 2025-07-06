<?php
namespace App\Tests\Entity\Artist;

use App\Entity\Artist;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;

class ArtistTest extends TestCase
{
    public function testInitialCollectionsAreEmpty(): void
    {
        $artist = new Artist();

        $this->assertInstanceOf(Collection::class, $artist->getProductEditions());
        $this->assertCount(0, $artist->getProductEditions());

        $this->assertInstanceOf(Collection::class, $artist->getTracks());
        $this->assertCount(0, $artist->getTracks());
    }

    public function testIdCanBeSetAndGet(): void
    {
        $artist = new Artist();
        $artist->setId(10);

        $this->assertSame(10, $artist->getId());
    }

    public function testNameCanBeSetAndGet(): void
    {
        $artist = new Artist();
        $artist->setName('The Beatles');

        $this->assertSame('The Beatles', $artist->getName());
    }

    public function testAddAndRemoveProductEdition(): void
    {
        $artist = new Artist();
        $productEdition = new ProductEditionStub();

        $artist->addProductEdition($productEdition);
        $this->assertCount(1, $artist->getProductEditions());
        $this->assertTrue($artist->getProductEditions()->contains($productEdition));

        $artist->removeProductEdition($productEdition);
        $this->assertCount(0, $artist->getProductEditions());
    }

    public function testAddAndRemoveTrack(): void
    {
        $artist = new Artist();
        $track = new TrackStub();

        $artist->addTrack($track);
        $this->assertCount(1, $artist->getTracks());
        $this->assertTrue($artist->getTracks()->contains($track));

        $artist->removeTrack($track);
        $this->assertCount(0, $artist->getTracks());
    }

    public function testToArrayReturnsExpectedData(): void
    {
        $artist = new Artist();
        $artist->setId(1);
        $artist->setName('Queen');

        $expected = [
            'id' => 1,
            'name' => 'Queen',
        ];

        $this->assertSame($expected, $artist->toArray());
    }
}