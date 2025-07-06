<?php
namespace App\Tests\DTO\Product;

use PHPUnit\Framework\TestCase;
use App\DTO\Product\ProductTagDto;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductTagDtoTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testValidDtoWithId(): void
    {
        $dto = new ProductTagDto(10, 'Etiqueta válida');
        $this->assertSame(10, $dto->id);
        $this->assertSame('Etiqueta válida', $dto->name);

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testValidDtoWithoutId(): void
    {
        $dto = new ProductTagDto(null, 'Nombre válido');
        
        $this->assertSame(null, $dto->id);
        $this->assertSame('Nombre válido', $dto->name);

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testNegativeId(): void
    {
        $dto = new ProductTagDto(-5, 'Etiqueta');
        $violations = $this->validator->validate($dto);
        $this->assertCount(1, $violations);
    }

    public function testBlankName(): void
    {
        $dto = new ProductTagDto(1, '');
        $violations = $this->validator->validate($dto);
        $this->assertCount(2, $violations);
    }

    public function testNameTooShort(): void
    {
        $dto = new ProductTagDto(1, 'No');
        $violations = $this->validator->validate($dto);
        $this->assertCount(1, $violations);
    }

    public function testNameTooLong(): void
    {
        $longName = str_repeat('a', 51);
        $dto = new ProductTagDto(1, $longName);
        $violations = $this->validator->validate($dto);
        $this->assertCount(1, $violations);
    }
}