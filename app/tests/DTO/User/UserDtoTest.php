<?php
namespace App\Tests\DTO\User;

use App\Entity\User;
use App\DTO\User\UserDto;
use PHPUnit\Framework\TestCase;

class UserDtoTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $dto = new UserDto(
            id: 123,
            email: 'user@example.com',
            name: 'John Doe',
            roles: ['ROLE_USER', 'ROLE_ADMIN'],
            password: 'hashedpassword'
        );

        $this->assertSame(123, $dto->id);
        $this->assertSame('user@example.com', $dto->email);
        $this->assertSame('John Doe', $dto->name);
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $dto->roles);
        $this->assertSame('hashedpassword', $dto->password);
    }

    public function testFromEntityCreatesDtoCorrectly(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setName('John Doe');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('hashedpassword');

        $dto = UserDto::fromEntity($user);

        $this->assertNull($dto->id);
        $this->assertSame('user@example.com', $dto->email);
        $this->assertSame('John Doe', $dto->name);
        $this->assertSame(['ROLE_USER'], $dto->roles);
        $this->assertSame('hashedpassword', $dto->password);
    }
}