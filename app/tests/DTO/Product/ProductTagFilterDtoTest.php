<?php
namespace App\Tests\DTO\Product;

use PHPUnit\Framework\TestCase;
use App\DTO\Product\ProductTagFilterDto;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductTagFilterDtoTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function test_default_values_are_valid(): void
    {
        $dto = new ProductTagFilterDto();
        $violations = $this->validator->validate($dto);

        $this->assertCount(0, $violations);
    }

    public function test_custom_valid_values(): void
    {
        $dto = new ProductTagFilterDto(page: 3, limit: 50);
        $violations = $this->validator->validate($dto);

        $this->assertCount(0, $violations);
    }

    public function test_invalid_page_less_than_one(): void
    {
        $dto = new ProductTagFilterDto(page: 0, limit: 10);
        $violations = $this->validator->validate($dto);

        $this->assertCount(1, $violations);
        $this->assertSame('La página debe ser al menos 1.', $violations[0]->getMessage());
    }

    public function test_invalid_limit_less_than_min(): void
    {
        $dto = new ProductTagFilterDto(page: 1, limit: 0);
        $violations = $this->validator->validate($dto);

        $this->assertCount(1, $violations);
        $this->assertSame('El límite debe estar entre 1 y 100.', $violations[0]->getMessage());
    }

    public function test_invalid_limit_greater_than_max(): void
    {
        $dto = new ProductTagFilterDto(page: 1, limit: 150);
        $violations = $this->validator->validate($dto);

        $this->assertCount(1, $violations);
        $this->assertSame('El límite debe estar entre 1 y 100.', $violations[0]->getMessage());
    }

    public function test_it_throws_type_error_with_invalid_types(): void
    {
        $this->expectException(\TypeError::class);
        new ProductTagFilterDto(page: 'one', limit: 'ten');
    }
}