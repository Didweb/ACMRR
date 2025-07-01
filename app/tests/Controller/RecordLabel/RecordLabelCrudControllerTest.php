<?php
namespace App\Tests\Controller\RecordLabel;

use App\Entity\User;
use App\Entity\RecordLabel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RecordLabelCrudControllerTest extends WebTestCase
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
        $this->client->request('GET', '/admin/record/label/list');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table'); 
        $this->assertSelectorTextContains('h1', 'Sellos'); 
    }

    public function testNewRecordLabelFormDisplayAndSubmit()
    {

        $crawler = $this->client->request('GET', '/admin/record/label/new');
        $name = 'Record Label'.time();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name=record_label_form]');

        $form = $crawler->selectButton('Guardar')->form();
        $form['record_label_form[name]'] = $name;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/record/label/list');
        $crawler = $this->client->followRedirect();

        $tbodyText = $crawler->filter('table tbody')->text();
        $this->assertStringContainsString($name, $tbodyText);

        $recordLabel = $this->entityManager->getRepository(RecordLabel::class)->findOneBy(['name' => $name]);

        $this->assertEquals($name, $recordLabel->getName());
    }

    public function testDeleteRecordLabelFormDisplayAndSubmit()
    {
        $artist = new RecordLabel();
        $artist->setName('Sello a eliminar '.time());
        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/admin/record/label/' . $artist->getId() . '/edit');
            $this->assertResponseIsSuccessful();

        $token = $crawler->filter('input[name="_token"]')->attr('value');


        $this->client->request('POST', '/admin/record/label/' . $artist->getId().'/delete', [
            '_method' => 'POST',
            '_token' => $token,
        ]);

        $this->assertResponseRedirects('/admin/record/label/list');
        $this->client->followRedirect();

        $deletedArtist = $this->entityManager
            ->getRepository(RecordLabel::class)
            ->find($artist->getId());

        $this->assertNull($deletedArtist);
    }

    protected function tearDown(): void
    {

        if ($this->entityManager !== null) {
                $connection = $this->entityManager->getConnection();
                $connection->executeStatement('DELETE FROM record_label');
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