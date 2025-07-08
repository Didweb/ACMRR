<?php
namespace App\Tests\Controller\Riddim;

use App\Entity\User;
use App\Entity\Riddim;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RiddimControllerTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $entityManager;
    private User $testUser;
    private $urlGenerator;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = self::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);

        // Eliminar usuario test si ya existe (para evitar conflictos)
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        if ($existingUser) {
            $this->entityManager->remove($existingUser);
            $this->entityManager->flush();
        }

        // Crear usuario test con ROLE_SUPER_ADMIN para poder acceder a /admin rutas
        $this->testUser = new User();
        $this->testUser->setEmail('test@example.com');
        $this->testUser->setName('Usuario Test');
        $this->testUser->setRoles(['ROLE_SUPER_ADMIN']);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $this->testUser->setPassword($passwordHasher->hashPassword($this->testUser, 'password'));

        $this->entityManager->persist($this->testUser);
        $this->entityManager->flush();

        // Loguear usuario
        $this->client->loginUser($this->testUser);
        $this->urlGenerator = self::getContainer()->get(UrlGeneratorInterface::class);
    }

    public function testIndexPageLoads(): void
    {
        $url = $this->urlGenerator->generate('app_riddim_index');
        $crawler = $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table'); // Asumiendo que hay una tabla en el listado
    }

    public function testNewRiddim(): void
    {
        $url = $this->urlGenerator->generate('app_riddim_new');
        $crawler = $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Guardar')->form(); 

        $form['riddim_form[name]'] = 'Test Riddim';

        $this->client->submit($form);

        // Comprobar redirección a índice
        $url = $this->urlGenerator->generate('app_riddim_index');
        $this->assertResponseRedirects($url);

        $this->client->followRedirect();


        // Verificar que el Riddim se haya guardado en BD
        $riddim = $this->entityManager->getRepository(Riddim::class)->findOneBy(['name' => 'Test Riddim']);
        $this->assertNotNull($riddim);
    }

    public function testEditRiddim(): void
    {
        // Primero crear un Riddim
        $riddim = new Riddim();
        $riddim->setName('Riddim para editar');
        $this->entityManager->persist($riddim);
        $this->entityManager->flush();

        $url = $this->urlGenerator->generate('app_riddim_edit', ['id' => $riddim->getId()]);
        $crawler = $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Guardar')->form();

        $form['riddim_form[name]'] = 'Riddim editado';

        $this->client->submit($form);

        $url = $this->urlGenerator->generate('app_riddim_index');
        $this->assertResponseRedirects($url);

        $this->client->followRedirect();

        $editedRiddim = $this->entityManager->getRepository(Riddim::class)->find($riddim->getId());
        $this->assertEquals('Riddim editado', $editedRiddim->getName());
    }

    public function testDeleteRiddim(): void
    {
        $riddim = new Riddim();
        $riddim->setName('Riddim a borrar');
        $this->entityManager->persist($riddim);
        $this->entityManager->flush();

        $url = $this->urlGenerator->generate('app_riddim_edit', ['id' => $riddim->getId()]);
        $crawler = $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();

        $token = $crawler->filter('input[name="_token"]')->attr('value');

        $url = $this->urlGenerator->generate('app_riddim_delete', ['id' => $riddim->getId()]);

        $this->client->request('POST', $url, [
            '_method' => 'POST',
            '_token' => $token,
        ]);

        $url = $this->urlGenerator->generate('app_riddim_index');
        $this->assertResponseRedirects($url);
        $this->client->followRedirect();

        $deleted = $this->entityManager->getRepository(Riddim::class)->find($riddim->getId());
        $this->assertNull($deleted);
    }
}