<?php
namespace App\DTO\Images;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageUploadDto
{
    public function __construct(
        #[Assert\NotNull(message: 'El ID de entidad es obligatorio.')]
        #[Assert\Positive(message: 'El ID debe ser un número positivo.')]
        public readonly ?int $entityId,

        #[Assert\NotBlank(message: 'El nombre de entidad no puede estar vacío.')]
        #[Assert\Length(
            max: 255,
            maxMessage: 'El nombre de entidad no puede tener más de {{ limit }} caracteres.'
        )]
        public readonly ?string $entityName,

        #[Assert\NotNull(message: 'La imagen es obligatoria.')]
        #[Assert\File(
            maxSize: '2M',
            mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
            mimeTypesMessage: 'Solo se permiten imágenes JPEG, PNG o WEBP.',
            maxSizeMessage: 'La imagen no puede pesar más de {{ limit }}.'
        )]
        public readonly UploadedFile $image
    ) {}    
}