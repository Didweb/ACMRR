<?php
namespace App\Tests\DTO\Product;

use App\DTO\Product\ProductFilterDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductFilterDtoTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get('validator');
    }

    public function testValidDtoHasNoViolations(): void
    {
        $dto = new ProductFilterDto(page: 1, limit: 10);
        $violations = $this->validator->validate($dto);

        $this->assertCount(0, $violations);
    }

    public function testPageMustBeAtLeastOne(): void
    {
        $dto = new ProductFilterDto(page: 0, limit: 10);
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, count($violations));
        $this->assertSame('La página debe ser al menos 1.', $violations[0]->getMessage());
    }

    public function testLimitMustBeInRange(): void
    {
        // límite menor que mínimo
        $dtoLow = new ProductFilterDto(page: 1, limit: 0);
        $violationsLow = $this->validator->validate($dtoLow);
        $this->assertGreaterThan(0, count($violationsLow));
        $this->assertStringContainsString('El límite debe estar entre', $violationsLow[0]->getMessage());

        // límite mayor que máximo
        $dtoHigh = new ProductFilterDto(page: 1, limit: 101);
        $violationsHigh = $this->validator->validate($dtoHigh);
        $this->assertGreaterThan(0, count($violationsHigh));
        $this->assertStringContainsString('El límite debe estar entre', $violationsHigh[0]->getMessage());
    }
}