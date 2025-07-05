<?php
namespace App\Tests\DTO\Product;

use PHPUnit\Framework\TestCase;
use App\DTO\Product\ProductTitleDto;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class ProductTitleDtoTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
    }

    // Constraints definidas manualmente (copiadas de ProductTitleDto)
    private function getConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'id' => [
                new Assert\Positive(message: 'El ID debe ser un número positivo.'),
            ],
            'name' => [
                new Assert\NotBlank(message: 'El nombre del producto es obligatorio.'),
                new Assert\Length(
                    max: 255,
                    maxMessage: 'El nombre no puede superar los {{ limit }} caracteres.'
                ),
            ],
            'productEditions' => [
                new Assert\Type('array', message: 'Las ediciones deben ser un array.'),
                new Assert\All([
                    new Assert\Type('array', message: 'Cada edición debe ser un array.'),
                ]),
            ],
        ]);
    }

    public function testValidDto()
    {
        $dto = new ProductTitleDto(
            id: 1,
            name: 'Producto válido',
            productEditions: [
                ['edition' => 'Primera'],
                ['edition' => 'Segunda'],
            ]
        );

        // Validamos usando un array con las propiedades del DTO
        $errors = $this->validator->validate([
            'id' => $dto->id,
            'name' => $dto->name,
            'productEditions' => $dto->productEditions,
        ], $this->getConstraints());

        $this->assertCount(0, $errors);
    }

    public function testIdNegative()
    {
        $dto = new ProductTitleDto(
            id: -5,
            name: 'Producto',
            productEditions: []
        );

        $errors = $this->validator->validate([
            'id' => $dto->id,
            'name' => $dto->name,
            'productEditions' => $dto->productEditions,
        ], $this->getConstraints());

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El ID debe ser un número positivo.', (string)$errors);
    }

    public function testNameBlank()
    {
        $dto = new ProductTitleDto(
            id: 1,
            name: '',
            productEditions: []
        );

        $errors = $this->validator->validate([
            'id' => $dto->id,
            'name' => $dto->name,
            'productEditions' => $dto->productEditions,
        ], $this->getConstraints());

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El nombre del producto es obligatorio.', (string)$errors);
    }

    public function testNameTooLong()
    {
        $longName = str_repeat('a', 256);
        $dto = new ProductTitleDto(
            id: 1,
            name: $longName,
            productEditions: []
        );

        $errors = $this->validator->validate([
            'id' => $dto->id,
            'name' => $dto->name,
            'productEditions' => $dto->productEditions,
        ], $this->getConstraints());

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El nombre no puede superar los 255 caracteres.', (string)$errors);
    }

    public function testProductEditionsNotArray()
    {
        $data = [
            'id' => 1,
            'name' => 'Producto',
            'productEditions' => 'no es un array',  // Aquí pasamos directamente el dato incorrecto
        ];

        $errors = $this->validator->validate($data, $this->getConstraints());

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Las ediciones deben ser un array.', (string)$errors);
    }

    public function testProductEditionsElementsNotArray()
    {
        $dto = new ProductTitleDto(
            id: 1,
            name: 'Producto',
            productEditions: ['no es array', 123]
        );

        $errors = $this->validator->validate([
            'id' => $dto->id,
            'name' => $dto->name,
            'productEditions' => $dto->productEditions,
        ], $this->getConstraints());

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Cada edición debe ser un array.', (string)$errors);
    }
}