<?php
namespace App\Tests\Entity\Riddim;

use App\Entity\Track;
use App\Entity\Riddim;
use PHPUnit\Framework\TestCase;

class RiddimTest extends TestCase
{
    public function testCanCreateRiddimAndSetName()
    {
        $riddim = new Riddim();
        $riddim->setName('Dancehall Classic');

        $this->assertSame('Dancehall Classic', $riddim->getName());
        $this->assertCount(0, $riddim->getTracks());
    }

    public function testAddAndRemoveTracks()
    {
        $riddim = new Riddim();
        $track = new Track();
        $track->setTitle('Rub a dub');

        $riddim->addTrack($track);

        $this->assertCount(1, $riddim->getTracks());
        $this->assertSame($track, $riddim->getTracks()->first());
        $this->assertSame($riddim, $track->getRiddim()); 

        $riddim->removeTrack($track);

        $this->assertCount(0, $riddim->getTracks());
        $this->assertNull($track->getRiddim());
    }
}