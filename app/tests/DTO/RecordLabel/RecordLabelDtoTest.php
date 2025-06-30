<?php
namespace App\Tests\DTO\RecordLabel;

use App\Entity\RecordLabel;
use PHPUnit\Framework\TestCase;
use App\DTO\RecordLabel\RecordLabelDto;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecordLabelDtoTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testCanBeCreatedDirectly(): void
    {
        $dto = new RecordLabelDto(1, 'Sony Music');

        $this->assertSame(1, $dto->id);
        $this->assertSame('Sony Music', $dto->name);
    }

    public function testCanBeCreatedFromEntity(): void
    {
        $recordLabel = new RecordLabel();
        $recordLabel->getName('Universal');

        $dto = RecordLabelDto::fromEntity($recordLabel);

        $this->assertNull($dto->id);
        $this->assertSame('Universal', $dto->name);
    }

    public function testNameNotBlankValidation(): void
    {
        $dto = new RecordLabelDto(null, '');

        $violations = $this->validator->validate($dto);
        $this->assertGreaterThanOrEqual(1, count($violations));

        $messages = array_map(fn($violation) => $violation->getMessage(), iterator_to_array($violations));
        $this->assertContains('El nombre no puede estar vacío', $messages);
    }

    public function testNameMinLengthValidation(): void
    {
        $dto = new RecordLabelDto(null, 'A');

        $violations = $this->validator->validate($dto);
        $this->assertCount(1, $violations);

        $this->assertStringContainsString('al menos 2 caracteres', $violations[0]->getMessage());
    }

    public function testNameMaxLengthValidation(): void
    {
        $longName = str_repeat('a', 256);
        $dto = new RecordLabelDto(null, $longName);

        $violations = $this->validator->validate($dto);
        $this->assertCount(1, $violations);

        $this->assertStringContainsString('no puede tener más de 255 caracteres', $violations[0]->getMessage());
    }

    public function testValidNamePassesValidation(): void
    {
        $dto = new RecordLabelDto(null, 'Valid Name');

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }
}