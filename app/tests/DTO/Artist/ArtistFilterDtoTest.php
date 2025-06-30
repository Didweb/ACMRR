<?php
namespace App\Tests\DTO\Artist;

use PHPUnit\Framework\TestCase;
use App\DTO\Artist\ArtistFilterDto;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArtistFilterDtoTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testDefaultValuesAreValid(): void
    {
        $dto = new ArtistFilterDto();

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testValidCustomValues(): void
    {
        $dto = new ArtistFilterDto(page: 5, limit: 50);

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testPageCannotBeLessThan1(): void
    {
        $dto = new ArtistFilterDto(page: 0);

        $violations = $this->validator->validate($dto);

        $this->assertCount(1, $violations);
        $this->assertSame('La página debe ser al menos 1.', $violations[0]->getMessage());
        $this->assertSame('page', $violations[0]->getPropertyPath());
    }

    public function testLimitCannotBeLessThan1(): void
    {
        $dto = new ArtistFilterDto(limit: 0);

        $violations = $this->validator->validate($dto);

        $this->assertCount(1, $violations);
        $this->assertStringContainsString('El límite debe estar entre', $violations[0]->getMessage());
        $this->assertSame('limit', $violations[0]->getPropertyPath());
    }

    public function testLimitCannotBeGreaterThan100(): void
    {
        $dto = new ArtistFilterDto(limit: 101);

        $violations = $this->validator->validate($dto);

        $this->assertCount(1, $violations);
        $this->assertStringContainsString('El límite debe estar entre', $violations[0]->getMessage());
        $this->assertSame('limit', $violations[0]->getPropertyPath());
    }
}