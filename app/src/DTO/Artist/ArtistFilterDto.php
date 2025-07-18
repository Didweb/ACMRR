<?php
namespace App\DTO\Artist;

use Symfony\Component\Validator\Constraints as Assert;

final class ArtistFilterDto
{
    public function __construct(
        #[Assert\GreaterThanOrEqual(1, message: "La página debe ser al menos 1.")]
        public readonly int $page = 1,

        #[Assert\Range(
            notInRangeMessage: 'El límite debe estar entre {{ min }} y {{ max }}.',
            min: 1,
            max: 100
        )]
        public readonly int $limit = 10
    ) {}
}