<?php
namespace App\DTO\RecordLabel;

use App\Entity\RecordLabel;
use Symfony\Component\Validator\Constraints as Assert;

class RecordLabelDto
{
    public function __construct(
        public readonly ?int $id,

        #[Assert\NotBlank(message: 'El nombre no puede estar vacío')]
        #[Assert\Length(
            min: 2,
            max: 255,
            minMessage: 'El nombre debe tener al menos {{ limit }} caracteres',
            maxMessage: 'El nombre no puede tener más de {{ limit }} caracteres'
        )]
        public readonly string $name
    ) {}

    public static function fromEntity(RecordLabel $recordLabel): self 
    {
        return new self(
            null,
            $recordLabel->getName()
        );
    }
}