<?php
namespace App\Tests\DTO\Product;

use PHPUnit\Framework\TestCase;
use App\DTO\Product\ProductEditionDto;
use App\ValueObject\Product\ProductFormat;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints as Assert;

class ProductEditionDtoTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
    }

    // Constraints manuales que imitan las del DTO
    private function getConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'id' => [
                new Assert\Positive(message: 'El ID debe ser un número positivo.'),
            ],
            'title' => [
                new Assert\NotNull(message: 'El título es obligatorio.'),
                new Assert\Type('array'),
            ],
            'label' => [
                new Assert\NotBlank(message: 'El sello (label) es obligatorio.'),
                new Assert\Type('string'),
                new Assert\Length(
                    max: 255,
                    maxMessage: 'El sello no puede tener más de {{ limit }} caracteres.'
                ),
            ],
            'year' => [
                new Assert\NotNull(message: 'El año es obligatorio.'),
                new Assert\Range(
                    min: 1900,
                    max: 2100,
                    notInRangeMessage: 'El año debe estar entre {{ min }} y {{ max }}.'
                ),
            ],
            'format' => [
                new Assert\NotBlank(message: 'El formato es obligatorio.'),
                new Assert\Choice(
                    callback: [ProductFormat::class, 'choicesStr'],
                    message: 'El formato "{{ value }}" no es válido.'
                ),
            ],
            'barcode' => [
                new Assert\Length(
                    max: 255,
                    maxMessage: 'El código de barras no puede tener más de {{ limit }} caracteres.'
                ),
            ],
            'stockNew' => [
                new Assert\PositiveOrZero(message: 'El stock debe ser cero o mayor.'),
            ],
            'priceNew' => [
                new Assert\PositiveOrZero(message: 'El precio debe ser cero o mayor.'),
            ],
            'productUsedItems' => [
                new Assert\Type('array'),
            ],
            'artists' => [
                new Assert\Type('array'),
            ],
            'tracks' => [
                new Assert\Type('array'),
            ],
        ]);
    }

    public function testValidDto()
    {
        $dto = new ProductEditionDto(
            id: null,
            title: ['id' => 10, 'name' => 'Título Ejemplo'],
            label: 'Sello Ejemplo',
            year: 2020,
            format: 'LP', 
            barcode: '1234567890123',
            stockNew: 10,
            priceNew: 99.99,
            productUsedItems: [],
            artists: [],
            tracks: []
        );

        $errors = $this->validator->validate([
            'id' => $dto->id,
            'title' => $dto->title,
            'label' => $dto->label,
            'year' => $dto->year,
            'format' => $dto->format,
            'barcode' => $dto->barcode,
            'stockNew' => $dto->stockNew,
            'priceNew' => $dto->priceNew,
            'productUsedItems' => $dto->productUsedItems,
            'artists' => $dto->artists,
            'tracks' => $dto->tracks,
        ], $this->getConstraints());

        $this->assertCount(0, $errors);
    }

    public function testIdNotPositive()
    {
        $dto = new ProductEditionDto(
            id: 0,
            title: ['id' => 10, 'name' => 'Título'],
            label: 'Sello',
            year: 2020,
            format: 'CD',
            barcode: null,
            stockNew: 0,
            priceNew: 0.0,
            productUsedItems: [],
            artists: [],
            tracks: []
        );

        $errors = $this->validator->validate(['id' => $dto->id], $this->getConstraints()['fields']['id']);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El ID debe ser un número positivo.', (string)$errors);
    }

    public function testTitleNull()
    {
        $dto = new ProductEditionDto(
            id: 1,
            title: null,
            label: 'Sello',
            year: 2020,
            format: 'CD',
            barcode: null,
            stockNew: 0,
            priceNew: 0.0,
            productUsedItems: [],
            artists: [],
            tracks: []
        );

        $errors = $this->validator->validate(['title' => $dto->title], $this->getConstraints()['fields']['title']);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El título es obligatorio.', (string)$errors);
    }

    public function testLabelBlank()
    {
        $dto = new ProductEditionDto(
            id: 1,
            title: ['id' => 10, 'name' => 'Título'],
            label: '',
            year: 2020,
            format: 'CD',
            barcode: null,
            stockNew: 0,
            priceNew: 0.0,
            productUsedItems: [],
            artists: [],
            tracks: []
        );

        $errors = $this->validator->validate(['label' => $dto->label], $this->getConstraints()['fields']['label']);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El sello (label) es obligatorio.', (string)$errors);
    }

    public function testYearOutOfRange()
    {
        $dto = new ProductEditionDto(
            id: 1,
            title: ['id' => 10, 'name' => 'Título'],
            label: 'Sello',
            year: 1800,
            format: 'CD',
            barcode: null,
            stockNew: 0,
            priceNew: 0.0,
            productUsedItems: [],
            artists: [],
            tracks: []
        );

        $errors = $this->validator->validate(['year' => $dto->year], $this->getConstraints()['fields']['year']);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El año debe estar entre 1900 y 2100.', (string)$errors);
    }

    public function testFormatInvalid()
    {
        $dto = new ProductEditionDto(
            id: 1,
            title: ['id' => 10, 'name' => 'Título'],
            label: 'Sello',
            year: 2020,
            format: 'INVALID_FORMAT',
            barcode: null,
            stockNew: 0,
            priceNew: 0.0,
            productUsedItems: [],
            artists: [],
            tracks: []
        );

        $errors = $this->validator->validate(['format' => $dto->format], $this->getConstraints()['fields']['format']);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('no es válido', (string)$errors);
    }

    public function testBarcodeTooLong()
    {
        $longBarcode = str_repeat('1', 256);

        $dto = new ProductEditionDto(
            id: 1,
            title: ['id' => 10, 'name' => 'Título'],
            label: 'Sello',
            year: 2020,
            format: 'CD',
            barcode: $longBarcode,
            stockNew: 0,
            priceNew: 0.0,
            productUsedItems: [],
            artists: [],
            tracks: []
        );

        $errors = $this->validator->validate(['barcode' => $dto->barcode], $this->getConstraints()['fields']['barcode']);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('no puede tener más de 255 caracteres', (string)$errors);
    }

    public function testStockNewNegative()
    {
        $dto = new ProductEditionDto(
            id: 1,
            title: ['id' => 10, 'name' => 'Título'],
            label: 'Sello',
            year: 2020,
            format: 'CD',
            barcode: null,
            stockNew: -1,
            priceNew: 0.0,
            productUsedItems: [],
            artists: [],
            tracks: []
        );

        $errors = $this->validator->validate(['stockNew' => $dto->stockNew], $this->getConstraints()['fields']['stockNew']);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El stock debe ser cero o mayor.', (string)$errors);
    }

    public function testPriceNewNegative()
    {
        $dto = new ProductEditionDto(
            id: 1,
            title: ['id' => 10, 'name' => 'Título'],
            label: 'Sello',
            year: 2020,
            format: 'CD',
            barcode: null,
            stockNew: 0,
            priceNew: -5.0,
            productUsedItems: [],
            artists: [],
            tracks: []
        );

        $errors = $this->validator->validate(['priceNew' => $dto->priceNew], $this->getConstraints()['fields']['priceNew']);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('El precio debe ser cero o mayor.', (string)$errors);
    }
    // Falta validar si no son null o Array lo campos finales
    public function testProductUsedItemsNotArray()
{
    $dto = new ProductEditionDto(
        id: 1,
        title: ['id' => 10, 'name' => 'Título'],
        label: 'Sello',
        year: 2020,
        format: 'CD',
        barcode: null,
        stockNew: 0,
        priceNew: 0.0,
        productUsedItems: 'no es un array',
        artists: [],
        tracks: []
    );

    $errors = $this->validator->validate(['productUsedItems' => $dto->productUsedItems], $this->getConstraints()['fields']['productUsedItems']);

    $this->assertNotEmpty($errors);
    $this->assertStringContainsString('This value should be of type array', (string)$errors);
}

public function testArtistsNotArray()
{
    $dto = new ProductEditionDto(
        id: 1,
        title: ['id' => 10, 'name' => 'Título'],
        label: 'Sello',
        year: 2020,
        format: 'CD',
        barcode: null,
        stockNew: 0,
        priceNew: 0.0,
        productUsedItems: [],
        artists: 'no es un array',
        tracks: []
    );

    $errors = $this->validator->validate(['artists' => $dto->artists], $this->getConstraints()['fields']['artists']);

    $this->assertNotEmpty($errors);
    $this->assertStringContainsString('This value should be of type array', (string)$errors);
}

public function testTracksNotArray()
{
    $dto = new ProductEditionDto(
        id: 1,
        title: ['id' => 10, 'name' => 'Título'],
        label: 'Sello',
        year: 2020,
        format: 'CD',
        barcode: null,
        stockNew: 0,
        priceNew: 0.0,
        productUsedItems: [],
        artists: [],
        tracks: 'no es un array'
    );

    $errors = $this->validator->validate(['tracks' => $dto->tracks], $this->getConstraints()['fields']['tracks']);

    $this->assertNotEmpty($errors);
    $this->assertStringContainsString('This value should be of type array', (string)$errors);
}
}