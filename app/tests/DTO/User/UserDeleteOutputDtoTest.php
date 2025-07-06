<?php
namespace App\Tests\DTO\User;

use PHPUnit\Framework\TestCase;
use App\DTO\User\UserDeleteOutputDto;

class UserDeleteOutputDtoTest extends TestCase
{
    public function testCanInstantiateWithSuccessOnly(): void
    {
        $dto = new UserDeleteOutputDto(success: true);

        $this->assertTrue($dto->success);
        $this->assertNull($dto->message);
    }

    public function testCanInstantiateWithSuccessAndMessage(): void
    {
        $dto = new UserDeleteOutputDto(success: false, message: 'Error deleting user');

        $this->assertFalse($dto->success);
        $this->assertSame('Error deleting user', $dto->message);
    }
}