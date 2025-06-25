<?php
namespace App\Service\User;

use App\Entity\User;
use App\DTO\User\UserDto;
use App\Utils\ViolationHelper;
use App\DTO\User\UserDeleteDto;
use App\DTO\User\UserFilterDto;
use App\DTO\User\UserListItemDto;
use App\DTO\User\UserDeleteOutputDto;
use App\Exception\ValidationException;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Validator\ConstraintViolation;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudService
{
    public function __construct(
        private UserRepository $userRepository,
        private PaginatorInterface $paginator,
        private EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
        private CsrfTokenManagerInterface $csrfTokenManager) {}

    public function getPaginated(UserFilterDto $filter): PaginationInterface
    {
        $queryBuilder = $this->userRepository->createQueryBuilder('u')->orderBy('u.id', 'ASC');

        $pagination = $this->paginator->paginate(
            $queryBuilder, 
            $filter->page, 
            $filter->limit
        );

        $items = array_map(fn(User $u) => new UserListItemDto(
                            $u->getId(),
                            $u->getEmail(),
                            $u->getName(),
                            implode(', ', $u->getRoles())
                        ), (array) $pagination->getItems());

        $pagination->setItems($items);   
         
        return $pagination;
    }

    public function create(UserDto $userDto): UserDto
    {

        $violations = new ConstraintViolationList();

        if ($this->userRepository->findOneBy(['email' => $userDto->email])) {
            ViolationHelper::add($violations, 'Email Duplicate.', 'email', $userDto->email);
        }


        try {
            $user = new User();
            $user->setEmail($userDto->email);
            $user->setName($userDto->name);
            $user->setRoles($userDto->roles);
            
            if ($userDto->password) {
                $user->setPassword(
                    $this->hasher->hashPassword($user, $userDto->password)
                );
            }

            $this->em->persist($user);
            $this->em->flush();

        } catch(\Exception $e) {
            ViolationHelper::add($violations, $e->getMessage(), $e->getCode(), $userDto->email);
        }


        ViolationHelper::result($violations);

        return new UserDto(
                id: $user->getId(),
                email: $user->getEmail(),
                name: $user->getName(),
                roles: $user->getRoles()
        );
    }

    public function delete(UserDeleteDto $userDeleteDto): UserDeleteOutputDto
    {
        $expectedTokenId = 'delete'.$userDeleteDto->userId;

        $violations = new ConstraintViolationList();

        $user = $this->userRepository->find($userDeleteDto->userId);

        if (!$user) {
            ViolationHelper::add($violations, 'User not found.', 'userId', $userDeleteDto->userId);
        }

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($expectedTokenId, $userDeleteDto->csrfToken))) {
            ViolationHelper::add($violations, 'Invalid CSRF token.', 'csrfToken', $userDeleteDto->csrfToken);
        }

        ViolationHelper::result($violations);

        $this->em->remove($user);
        $this->em->flush();

        return new UserDeleteOutputDto(true);
    }
}