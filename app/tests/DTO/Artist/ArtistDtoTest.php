<?php
namespace App\Tests\DTO\Artist;

use App\Entity\Artist;
use App\DTO\Artist\ArtistDto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArtistDtoTest extends TestCase
{

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testItCanBeCreatedDirectly()
    {
        $dto = new ArtistDto(1, 'The Beatles');

        $this->assertSame(1, $dto->id);
        $this->assertSame('The Beatles', $dto->name);

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testItCanBeCreatedFromEntity()
    {
        $artist = new Artist();
        $artist->setId(42);
        $artist->setName('Queen');

        $dto = ArtistDto::fromEntity($artist);

        $this->assertInstanceOf(ArtistDto::class, $dto);
        $this->assertSame(42, $dto->id);
        $this->assertSame('Queen', $dto->name);

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testNullIdIsAccepted()
    {
        $dto = new ArtistDto(null, 'Nuevo Artista');

        $this->assertNull($dto->id);
        $this->assertSame('Nuevo Artista', $dto->name);

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testEmptyNameTriggersViolation()
    {
        $dto = new ArtistDto(null, '');

        $violations = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($violations));
        $this->assertSame('El nombre no puede estar vacío', $violations[0]->getMessage());
    }

    public function testTooShortNameTriggersViolation()
    {
        $dto = new ArtistDto(null, 'A');

        $violations = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($violations));
        $this->assertSame('El nombre debe tener al menos 2 caracteres', $violations[0]->getMessage());
    }

    public function testTooLongNameTriggersViolation()
    {
        $longName = str_repeat('a', 300);
        $dto = new ArtistDto(null, $longName);

        $violations = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($violations));
        $this->assertSame('El nombre no puede tener más de 255 caracteres', $violations[0]->getMessage());
    }
}