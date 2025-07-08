<?php
namespace App\Tests\DTO\Riddim;

use App\DTO\Riddim\RiddimFilterDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RiddimFilterDtoTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidDto(): void
    {
        $dto = new RiddimFilterDto(page: 1, limit: 10);
        $violations = $this->validator->validate($dto);

        $this->assertCount(0, $violations);
    }

    public function testInvalidPageBelowMinimum(): void
    {
        $dto = new RiddimFilterDto(page: 0, limit: 10);
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, count($violations));
        $this->assertSame('La página debe ser al menos 1.', $violations[0]->getMessage());
    }

    public function testInvalidLimitBelowMinimum(): void
    {
        $dto = new RiddimFilterDto(page: 1, limit: 0);
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, count($violations));
        $this->assertSame('El límite debe estar entre 1 y 100.', $violations[0]->getMessage());
    }

    public function testInvalidLimitAboveMaximum(): void
    {
        $dto = new RiddimFilterDto(page: 1, limit: 101);
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, count($violations));
        $this->assertSame('El límite debe estar entre 1 y 100.', $violations[0]->getMessage());
    }

    public function testDefaultValuesAreValid(): void
    {
        $dto = new RiddimFilterDto();
        $violations = $this->validator->validate($dto);

        $this->assertCount(0, $violations);
    }
}