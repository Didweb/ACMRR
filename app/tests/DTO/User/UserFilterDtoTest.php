<?php
namespace App\Tests\DTO\User;

use App\DTO\User\UserFilterDto;
use PHPUnit\Framework\TestCase;

class UserFilterDtoTest  extends TestCase
{
    public function testDefaultValues(): void
    {
        $dto = new UserFilterDto();

        $this->assertSame(1, $dto->page);
        $this->assertSame(10, $dto->limit);
    }

    public function testCustomValues(): void
    {
        $dto = new UserFilterDto(page: 5, limit: 20);

        $this->assertSame(5, $dto->page);
        $this->assertSame(20, $dto->limit);
    }
}