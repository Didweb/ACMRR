<?php
namespace App\Tests\DTO\RecordLabel;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use App\DTO\RecordLabel\RecordLabelFilterDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecordLabelFilterDtoTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testValidDefaults(): void
    {
        $dto = new RecordLabelFilterDto();

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testValidCustomValues(): void
    {
        $dto = new RecordLabelFilterDto(page: 5, limit: 50);

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testInvalidPageTooLow(): void
    {
        $dto = new RecordLabelFilterDto(page: 0, limit: 10);

        $violations = $this->validator->validate($dto);
        $this->assertCount(1, $violations);

        $violation = $violations[0];
        $this->assertSame('La página debe ser al menos 1.', $violation->getMessage());
    }

    public function testInvalidLimitTooLow(): void
    {
        $dto = new RecordLabelFilterDto(page: 1, limit: 0);

        $violations = $this->validator->validate($dto);
        $this->assertCount(1, $violations);

        $violation = $violations[0];
        $this->assertSame('El límite debe estar entre 1 y 100.', $violation->getMessage());
    }

    public function testInvalidLimitTooHigh(): void
    {
        $dto = new RecordLabelFilterDto(page: 1, limit: 101);

        $violations = $this->validator->validate($dto);
        $this->assertCount(1, $violations);

        $violation = $violations[0];
        $this->assertSame('El límite debe estar entre 1 y 100.', $violation->getMessage());
    }
}