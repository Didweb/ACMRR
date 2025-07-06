<?php
namespace App\Tests\Service\User;

use App\Entity\User;
use App\DTO\User\UserDto;
use App\DTO\User\UserDeleteDto;
use PHPUnit\Framework\TestCase;
use App\Exception\BusinessException;
use App\DTO\User\UserDeleteOutputDto;
use App\Service\User\UserCrudService;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudServiceTest extends TestCase
{

    private $userRepository;
    private $paginator;
    private $em;
    private $hasher;
    private $csrfTokenManager;
    private $userService;

    protected function setUp(): void
    {

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->hasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);

        $this->userService = new UserCrudService(
            $this->userRepository,
            $this->paginator,
            $this->em,
            $this->hasher,
            $this->csrfTokenManager
        );
    }

    public function testCreateUserSuccessfully()
    {
       $userDto = new UserDto(
                id: null,
                email: 'test@example.com',
                name: 'Test User',
                roles: ['ROLE_USER'],
                password: 'plainpassword'
                );

        $this->userRepository->method('findOneBy')->with(['email' => $userDto->email])->willReturn(null);
        $this->hasher->method('hashPassword')->willReturn('hashed_password');

        $user = null;

        $this->em->expects($this->once())->method('persist')->with($this->callback(function($u) use ($userDto, &$user) {
            $user = $u;
            $this->assertEquals($userDto->email, $u->getEmail());
            $this->assertEquals($userDto->name, $u->getName());
            $this->assertEquals($userDto->roles, $u->getRoles());
            $this->assertEquals('hashed_password', $u->getPassword());
            return true;
        }));

        $this->em->expects($this->once())->method('flush')->willReturnCallback(function() use (&$user) {
            $reflection = new \ReflectionClass($user);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($user, 123);
        });

        $result = $this->userService->create($userDto);

        $this->assertInstanceOf(UserDto::class, $result);
        $this->assertEquals($userDto->email, $result->email);
        $this->assertEquals($userDto->name, $result->name);
        $this->assertEquals($userDto->roles, $result->roles);
        $this->assertEquals(123, $result->id);

    }

    public function testCreateUserThrowsExceptionHandled()
    {
        $this->expectException(BusinessException::class);

        $userDto = new UserDto(
            id: null,
            email: 'test@example.com',
            name: 'Test User',
            roles: ['ROLE_USER'],
            password: 'plainpassword'
        );

        $this->userRepository
            ->method('findOneBy')
            ->with(['email' => $userDto->email])
            ->willReturn(null);

        $this->hasher
            ->method('hashPassword')
            ->willReturn('hashed_password');

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->willThrowException(new \Exception('DB error'));

        $this->userService->create($userDto);
    }

    public function testCreateUserThrowsBusinessExceptionOnDuplicateEmail(): void
    {
        $this->expectException(BusinessException::class);

        $userDto = new UserDto(
            id: null,
            email: 'existing@example.com',
            name: 'Existing User',
            roles: ['ROLE_USER'],
            password: 'plainpassword'
        );

        $existingUser = $this->createMock(User::class);

        $this->userRepository
            ->method('findOneBy')
            ->with(['email' => $userDto->email])
            ->willReturn($existingUser);

        $this->userService->create($userDto);
    }

    public function testDeleteSuccess(): void
    {
        $userId = 42;
        $csrfToken = 'valid_token';
        $expectedTokenId = 'delete' . $userId;

        $user = $this->createMock(User::class);
        $dto = new UserDeleteDto($userId, $csrfToken);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->csrfTokenManager->expects($this->once())
            ->method('isTokenValid')
            ->with($this->callback(function (CsrfToken $token) use ($expectedTokenId, $csrfToken) {
                return $token->getId() === $expectedTokenId && $token->getValue() === $csrfToken;
            }))
            ->willReturn(true);

        $this->em->expects($this->once())->method('remove')->with($user);
        $this->em->expects($this->once())->method('flush');

        $result = $this->userService->delete($dto);

        $this->assertInstanceOf(UserDeleteOutputDto::class, $result);
        $this->assertTrue($result->success);
    }

    public function testDeleteFailsWhenUserNotFound(): void
    {
        $userId = 42;
        $csrfToken = 'any_token';
        $dto = new UserDeleteDto($userId, $csrfToken);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn(null);

        $this->expectException(BusinessException::class);

        $this->userService->delete($dto);
    }

    public function testDeleteFailsWhenCsrfInvalid(): void
    {
        $userId = 42;
        $csrfToken = 'invalid_token';
        $expectedTokenId = 'delete' . $userId;

        $user = $this->createMock(User::class);
        $dto = new UserDeleteDto($userId, $csrfToken);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->csrfTokenManager->expects($this->once())
            ->method('isTokenValid')
            ->with($this->callback(function (CsrfToken $token) use ($expectedTokenId, $csrfToken) {
                return $token->getId() === $expectedTokenId && $token->getValue() === $csrfToken;
            }))
            ->willReturn(false); 

        $this->expectException(BusinessException::class);

        $this->userService->delete($dto);
    }
}