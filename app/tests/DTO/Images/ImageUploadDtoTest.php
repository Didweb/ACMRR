<?php
namespace App\Tests\DTO\Images;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\DTO\Images\ImageUploadDto;
use Symfony\Component\Validator\Constraints as Assert;

class ImageUploadDtoTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        // Creamos un validador simple que no usa mapeo de atributos
        $this->validator = Validation::createValidator();
    }

    private function createValidUploadedFile(): UploadedFile
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'upl');

        // PNG válido mínimo (1x1 píxel transparente)
        $pngData = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII='
        );

        file_put_contents($tempFile, $pngData);

        return new UploadedFile(
            $tempFile,
            'image.png',
            'image/png',
            null,
            true // indica modo test
        );
    }

    // Constraints definidas manualmente (copiadas de DTO)
    private function getConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'entityId' => [
                new Assert\NotNull(message: 'El ID de entidad es obligatorio.'),
                new Assert\Positive(message: 'El ID debe ser un número positivo.'),
            ],
            'entityName' => [
                new Assert\NotBlank(message: 'El nombre de entidad no puede estar vacío.'),
                new Assert\Length(
                    max: 255,
                    maxMessage: 'El nombre de entidad no puede tener más de {{ limit }} caracteres.'
                ),
            ],
            'image' => [
                new Assert\NotNull(message: 'La imagen es obligatoria.'),
                new Assert\File(
                    maxSize: '2M',
                    mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                    mimeTypesMessage: 'Solo se permiten imágenes JPEG, PNG o WEBP.',
                    maxSizeMessage: 'La imagen no puede pesar más de {{ limit }}.'
                )
            ],
        ]);
    }

    public function testValidDto()
    {
        $dto = new ImageUploadDto(
            entityId: 1,
            entityName: 'Entidad válida',
            image: $this->createValidUploadedFile()
        );

        $errors = $this->validator->validate([
            'entityId' => $dto->entityId,
            'entityName' => $dto->entityName,
            'image' => $dto->image,
        ], $this->getConstraints());

        $this->assertCount(0, $errors);
    }

    public function testEntityIdNotNull()
    {
        $dto = new ImageUploadDto(
            entityId: null,
            entityName: 'Entidad',
            image: $this->createValidUploadedFile()
        );

        $errors = $this->validator->validate([
            'entityId' => $dto->entityId,
            'entityName' => $dto->entityName,
            'image' => $dto->image,
        ], $this->getConstraints());

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El ID de entidad es obligatorio.', (string)$errors);
    }
}