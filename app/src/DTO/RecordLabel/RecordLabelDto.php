<?php
namespace App\DTO\RecordLabel;

use App\Entity\RecordLabel;

class RecordLabelDto
{
    public function __construct(
        public readonly ?int $id,
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