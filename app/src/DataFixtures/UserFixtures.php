<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $user = new User();
        $user->setEmail('info@raspinurecords.com');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setName('Edu');

        $password = getenv('SUPER_ADMIN_PASSWORD') ?: '123456';
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $manager->persist($user);
        $manager->flush();
    }
}