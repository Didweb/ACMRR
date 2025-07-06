<?php
namespace App\Tests\DTO\Product;

use App\Entity\Track;
use App\Entity\Artist;
use App\Entity\RecordLabel;
use App\Entity\ProductTitle;
use App\Entity\ProductEdition;
use App\Entity\ProductUsedItem;
use PHPUnit\Framework\TestCase;
use App\DTO\Product\ProductEditionDto;
use App\ValueObject\Product\ProductFormat;
use App\ValueObject\Product\ProductStatus;
use App\ValueObject\Product\ProductBarcode;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class ProductEditionDtoTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
    }

    // Constraints manuales que imitan las del DTO
    private function getConstraints(): array
    {
        return [
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
        ];
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
        ], new Assert\Collection($this->getConstraints()));

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

        $errors = $this->validator->validate($dto->id, $this->getConstraints()['id']);

        $this->assertNotEmpty($errors);
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

        $errors = $this->validator->validate($dto->title, $this->getConstraints()['title']);

        $this->assertNotEmpty($errors);
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

        $errors = $this->validator->validate($dto->label, $this->getConstraints()['label']);

        $this->assertNotEmpty($errors);
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

        $errors = $this->validator->validate($dto->year, $this->getConstraints()['year']);

        $this->assertNotEmpty($errors);
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

        $errors = $this->validator->validate($dto->format, $this->getConstraints()['format']);

        $this->assertNotEmpty($errors);
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

        $errors = $this->validator->validate($dto->barcode, $this->getConstraints()['barcode']);

        $this->assertNotEmpty($errors);
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

        $errors = $this->validator->validate($dto->stockNew, $this->getConstraints()['stockNew']);

        $this->assertNotEmpty($errors);
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

        $errors = $this->validator->validate($dto->priceNew, $this->getConstraints()['priceNew']);

        $this->assertNotEmpty($errors);
    }
   
    public function testFromEntity(): void
    {
        // Crear objetos reales
        $title = new ProductTitle();
        $title->setId(1);
        $title->setName('Test Album');

        $label = new RecordLabel();
        $label->setId(1);
        $label->setName('Test Label');


        $format = new ProductFormat('LP');
        $barcode = new ProductBarcode('4006381333931');

        $artist = new Artist('Test Artist');
        $artist->setId(1);
        $artist->setName('Test Artist');

       

        // Crear edición de producto
        $edition = new ProductEdition();
        $edition->setId(1);
        $edition->setTitle($title);
        $edition->setLabel($label);
        $edition->setFormat($format);
        $edition->setBarcode($barcode);
        $edition->addArtist($artist);
        $edition->setYear(2000);
        $edition->setPriceNew(19.99);
        $edition->setStockNew(10);


        $status = new ProductStatus('VG');
        $usedItem = new ProductUsedItem(); 
        $usedItem->setId(1); 
        $usedItem->setEdition($edition); 
        $usedItem->setPrice(10.5);
        $usedItem->setBarcode($barcode);
        $usedItem->setConditionVinyl($status);
        $usedItem->setConditionFolder($status);

        $track = new Track();
        $track->setId(1);
        $track->setTitle('Title');
        $track->setProductEdition($edition);
        $track->addArtist($artist);

        $edition->addProductUsedItem($usedItem);
        $edition->addTrack($track);

        // Ejecutar el método a probar
        $dto = ProductEditionDto::fromEntity($edition);

        // Verificaciones
        $this->assertEquals(1, $dto->id);
        $this->assertEquals(['id' => 1, 'name' => 'Test Album'], $dto->title);
        $this->assertEquals(1, $dto->label);
        $this->assertEquals(2000, $dto->year);
        $this->assertEquals('LP', $dto->format);
        $this->assertEquals('4006381333931', $dto->barcode);
        $this->assertEquals(10, $dto->stockNew);
        $this->assertEquals(19.99, $dto->priceNew);
        $this->assertEquals([ $usedItem->toArray() ], $dto->productUsedItems);
        $this->assertEquals([ $artist->toArray() ], $dto->artists);
        $this->assertEquals([$track->toArray()], $dto->tracks);
    }
    
}