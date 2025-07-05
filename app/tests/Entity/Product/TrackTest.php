<?php
namespace App\Tests\Entity\Product;

use App\Entity\Track;
use App\Entity\Artist;
use App\Entity\Riddim;
use App\Entity\ProductEdition;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class TrackTest extends TestCase
{
    public function testInitialState()
    {
        $track = new Track();

        // id no se puede probar porque es privado y sin setter (normal en Doctrine)
        $this->assertInstanceOf(ArrayCollection::class, $track->getArtists());
        $this->assertCount(0, $track->getArtists());
        $this->assertNull($track->getProductEdition());
        $this->assertNull($track->getRiddim());
    }

    public function testSetAndGetTitle()
    {
        $track = new Track();
        $track->setTitle('My Track Title');
        $this->assertSame('My Track Title', $track->getTitle());
    }

    public function testSetAndGetProductEdition()
    {
        $track = new Track();
        $edition = new ProductEdition();
        $track->setProductEdition($edition);
        $this->assertSame($edition, $track->getProductEdition());

        $track->setProductEdition(null);
        $this->assertNull($track->getProductEdition());
    }

    public function testAddAndRemoveArtist()
    {
        $track = new Track();
        $artist = new Artist();

        // Al principio colección vacía
        $this->assertCount(0, $track->getArtists());

        // Añadimos artista
        $track->addArtist($artist);
        $this->assertCount(1, $track->getArtists());
        $this->assertTrue($track->getArtists()->contains($artist));
        $this->assertTrue($artist->getTracks()->contains($track)); // el artista apunta al track también

        // Añadir el mismo artista no duplica
        $track->addArtist($artist);
        $this->assertCount(1, $track->getArtists());

        // Eliminar artista
        $track->removeArtist($artist);
        $this->assertCount(0, $track->getArtists());
        $this->assertFalse($artist->getTracks()->contains($track));

        // Remover artista que no está no da error ni cambia
        $track->removeArtist($artist);
        $this->assertCount(0, $track->getArtists());
    }

    public function testSetAndGetRiddim()
    {
        $track = new Track();
        $riddim = new Riddim();

        $track->setRiddim($riddim);
        $this->assertSame($riddim, $track->getRiddim());

        $track->setRiddim(null);
        $this->assertNull($track->getRiddim());
    }

    public function testToArray()
    {
        $track = new Track();
        $track->setTitle('Track Title');

        $artist = new Artist();
        $artist->setName('Artist Name');
        $track->addArtist($artist);

        $riddim = new Riddim();
        $riddim->setName('Riddim Name');
        $track->setRiddim($riddim);

        $array = $track->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertSame('Track Title', $array['title']);

        $this->assertIsArray($array['artists']);
        $this->assertCount(1, $array['artists']);
        $this->assertSame('Artist Name', $array['artists'][0]['name']); // asumiendo toArray() en Artist tiene 'name'

        $this->assertSame('Riddim Name', $array['riddim']);
    }
}