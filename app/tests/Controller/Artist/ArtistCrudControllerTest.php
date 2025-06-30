<?php
namespace App\Tests\Controller\Artist;

use App\Entity\User;
use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ArtistCrudControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $testUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);


         $existingUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'test@example.com']);

        if ($existingUser) {
            $this->entityManager->remove($existingUser);
            $this->entityManager->flush();
        }


        $this->testUser = new User();
        $this->testUser->setEmail('test@example.com');
        $this->testUser->setName('Usuario Test'); 
        $this->testUser->setRoles(['ROLE_SUPER_ADMIN']); 
        $this->testUser->setPassword(
            self::getContainer()->get(UserPasswordHasherInterface::class)->hashPassword($this->testUser, 'password')
        );

        $this->entityManager->persist($this->testUser);
        $this->entityManager->flush();

        $this->client->loginUser($this->testUser);
    }

    public function testIndexPageLoadsCorrectly()
    {
        $this->client->request('GET', '/admin/artist/list');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table'); 
        $this->assertSelectorTextContains('h1', 'Artistas'); 
    }

    public function testNewArtistFormDisplayAndSubmit()
    {
        $crawler = $this->client->request('GET', '/admin/artist/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name=artist_form]');

        $form = $crawler->selectButton('Guardar')->form();
        $form['artist_form[name]'] = 'Artista de Prueba';

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/artist/list');
        $crawler = $this->client->followRedirect();

        $this->assertSelectorExists('table');
        $this->assertSelectorExists('table tr:contains("Artista de Prueba")');

    
        $artist = $this->entityManager->getRepository(Artist::class)->findOneBy(['name' => 'Artista de Prueba']);
        $this->assertNotNull($artist);
    }

    protected function tearDown(): void
    {
        if ($this->testUser !== null && $this->entityManager !== null) {
            $managedUser = $this->entityManager->getRepository(User::class)->find($this->testUser->getId());
            if ($managedUser !== null) {
                $this->entityManager->remove($managedUser);
                $this->entityManager->flush();
            }
        }

        parent::tearDown();

        if ($this->entityManager !== null) {
            $this->entityManager->close();
            $this->entityManager = null;
        }
    }
}