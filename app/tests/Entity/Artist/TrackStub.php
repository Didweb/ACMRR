<?php
namespace App\Tests\Entity\Artist;

use App\Entity\Track;
use App\Entity\Artist;

class TrackStub extends Track
{
    private array $artists = [];

    public function addArtist(Artist $artist): self
    {
        $hash = spl_object_hash($artist);
        if (!isset($this->artists[$hash])) {
            $this->artists[$hash] = $artist;
        }
        return $this;
    }

    public function removeArtist(Artist $artist): self
    {
        $hash = spl_object_hash($artist);
        if (isset($this->artists[$hash])) {
            unset($this->artists[$hash]);
        }
        return $this;
    }
}