<?php
namespace App\Tests\DTO\User;

use PHPUnit\Framework\TestCase;
use App\DTO\User\UserListItemDto;

class UserListItemDtoTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $dto = new UserListItemDto(
            id: 123,
            email: 'user@example.com',
            name: 'John Doe',
            roles: 'ROLE_USER,ROLE_ADMIN'
        );

        $this->assertSame(123, $dto->id);
        $this->assertSame('user@example.com', $dto->email);
        $this->assertSame('John Doe', $dto->name);
        $this->assertSame('ROLE_USER,ROLE_ADMIN', $dto->roles);
    }
}