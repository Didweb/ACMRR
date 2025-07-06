<?php
namespace App\Tests\DTO\User;

use App\DTO\User\UserDeleteDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserDeleteDtoTest  extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get('validator');
    }

    public function testValidDtoHasNoViolations(): void
    {
        $dto = new UserDeleteDto(userId: 123, csrfToken: 'token123');
        $violations = $this->validator->validate($dto);

        $this->assertCount(0, $violations);
    }



    public function testCsrfTokenMustNotBeBlank(): void
    {
        $dto = new UserDeleteDto(userId: 1, csrfToken: '');
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, count($violations));
        $this->assertSame('This value should not be blank.', $violations[0]->getMessage());
    }
}