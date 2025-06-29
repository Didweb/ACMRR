<?php
namespace App\DTO\Images;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageUploadDto
{
    public function __construct(
        public readonly ?int $entityId,
        public readonly ?string $entityName,
        public readonly UploadedFile $image
    ) {}    
}