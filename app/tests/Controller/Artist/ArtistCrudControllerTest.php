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
    private Artist $artist;

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
        $name = 'Artista de Prueba'.time();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name=artist_form]');

        $form = $crawler->selectButton('Guardar')->form();
        $form['artist_form[name]'] = $name;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/artist/list');
        $crawler = $this->client->followRedirect();

        $tbodyText = $crawler->filter('table tbody')->text();
        $this->assertStringContainsString($name, $tbodyText);

        $this->artist = $this->entityManager->getRepository(Artist::class)->findOneBy(['name' => $name]);

        $this->assertEquals($name, $this->artist->getName());
    }

    public function testDeleteArtistFormDisplayAndSubmit()
    {
        $artist = new Artist();
        $artist->setName('Artista a eliminar '.time());
        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/admin/artist/' . $artist->getId() . '/edit');
            $this->assertResponseIsSuccessful();

        $token = $crawler->filter('input[name="_token"]')->attr('value');


        $this->client->request('POST', '/admin/artist/' . $artist->getId().'/delete', [
            '_method' => 'POST',
            '_token' => $token,
        ]);

        $this->assertResponseRedirects('/admin/artist/list');
        $this->client->followRedirect();

        $deletedArtist = $this->entityManager
            ->getRepository(Artist::class)
            ->find($artist->getId());

        $this->assertNull($deletedArtist);
    }

    protected function tearDown(): void
    {

        if ($this->entityManager !== null) {
                $connection = $this->entityManager->getConnection();
                $connection->executeStatement('DELETE FROM artist');
            }

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