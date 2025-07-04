<?php
namespace App\Tests\Controller\Product;

use App\Entity\User;
use App\Entity\ProductTag;
use App\Entity\RecordLabel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProductTagControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $testUser;
    private $urlGenerator;
    
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

        $this->urlGenerator = self::getContainer()->get(UrlGeneratorInterface::class);
    }


    public function testIndex(): void
    {
        $url = $this->urlGenerator->generate('app_product_product_tag_index');
        $crawler = $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table'); 
    }

    public function testNew(): void
    {
        $url = $this->urlGenerator->generate('app_product_product_tag_new');
        $crawler = $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $uniqueName = 'Test Tag ' . uniqid();

        $form = $crawler->selectButton('Guardar')->form([
            'product_tag_form[name]' => $uniqueName,
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();

        // Verifica si el nombre aparece en una tabla u otro lugar fiable
        $this->assertSelectorTextContains('table', $uniqueName);
    }

    public function testEdit(): void
    {
       $artist = new ProductTag();
        $artist->setName('Tag 1 '.time());
        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        $url = $this->urlGenerator->generate('app_product_product_tag_edit', ['id' => $artist->getId()]);
        $crawler = $this->client->request('GET', $url);
            $this->assertResponseIsSuccessful();

        $token = $crawler->filter('input[name="_token"]')->attr('value');

        $url = $this->urlGenerator->generate('app_record_label_delete', ['id' => $artist->getId()]);
        $this->client->request('POST', $url, [
            '_method' => 'POST',
            '_token' => $token,
        ]);

        $url = $this->urlGenerator->generate('app_record_label_index');
        $this->assertResponseRedirects($url);
        $this->client->followRedirect();

        $deletedArtist = $this->entityManager
            ->getRepository(RecordLabel::class)
            ->find($artist->getId());

        $this->assertNull($deletedArtist);
    }

    public function testDelete(): void
    {
        $productTag = new ProductTag();
        $productTag->setName('Tag a eliminar ' . time());
        $this->entityManager->persist($productTag);
        $this->entityManager->flush();

        // Ir a la vista de edición del tag
        $url = $this->urlGenerator->generate('app_product_product_tag_edit', ['id' => $productTag->getId()]);
        $crawler = $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();

        // Obtener el token CSRF del formulario
        $token = $crawler->filter('input[name="_token"]')->attr('value');

        // Hacer la petición POST de borrado
        $url = $this->urlGenerator->generate('app_product_product_tag_delete', ['id' => $productTag->getId()]);
        $this->client->request('POST', $url, [
            '_method' => 'POST',
            '_token' => $token,
        ]);

        // Verificar redirección y que el tag fue eliminado
        $url = $this->urlGenerator->generate('app_product_product_tag_index');
        $this->assertResponseRedirects($url);
        $this->client->followRedirect();

        $deletedTag = $this->entityManager
            ->getRepository(ProductTag::class)
            ->find($productTag->getId());

        $this->assertNull($deletedTag);
    }
}