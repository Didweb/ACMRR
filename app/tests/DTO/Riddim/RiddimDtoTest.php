<?php
namespace App\Tests\DTO\Riddim;

use App\DTO\Riddim\RiddimDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RiddimDtoTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidDto(): void
    {
        $dto = new RiddimDto(
            id: 1,
            name: 'Classic Riddim',
            tracks: [1, 2, 3]
        );

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations);
    }

    public function testInvalidId(): void
    {
        $dto = new RiddimDto(
            id: -5,
            name: 'Classic Riddim',
            tracks: []
        );

        $violations = $this->validator->validate($dto);
        $this->assertCount(1, $violations);
        $this->assertSame('El ID debe ser un número positivo.', $violations[0]->getMessage());
    }

    public function testBlankName(): void
    {
        $dto = new RiddimDto(
            id: 1,
            name: '',
            tracks: []
        );

        $violations = $this->validator->validate($dto);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('El nombre no puede estar vacío', $violations[0]->getMessage());
    }

    public function testShortName(): void
    {
        $dto = new RiddimDto(
            id: 1,
            name: 'A',
            tracks: []
        );

        $violations = $this->validator->validate($dto);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('El nombre debe tener al menos 2 caracteres', $violations[0]->getMessage());
    }

    public function testLongName(): void
    {
        $dto = new RiddimDto(
            id: 1,
            name: str_repeat('a', 256),
            tracks: []
        );

        $violations = $this->validator->validate($dto);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('El nombre no puede tener más de 255 caracteres', $violations[0]->getMessage());
    }

}