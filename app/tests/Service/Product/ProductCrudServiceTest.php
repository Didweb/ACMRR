<?php
namespace App\Tests\Service\Product;

use App\Entity\ProductImage;
use App\Entity\ProductTitle;
use App\Entity\ProductEdition;
use App\DTO\Product\ProductTitleDto;
use App\Exception\BusinessException;
use App\DTO\Product\ProductFilterDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Product\ProductCrudService;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\Product\ProductRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ProductCrudServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ProductCrudService $service;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->em = $container->get(EntityManagerInterface::class);
        $registry = $container->get('doctrine');
        $paginator = $container->get(PaginatorInterface::class);
        $csrfTokenManager = $container->get(CsrfTokenManagerInterface::class);

        $productRepository = new ProductRepository($registry);

        $this->service = new ProductCrudService(
            $productRepository,
            $paginator,
            $this->em,
            $csrfTokenManager
        );
    }

    protected function tearDown(): void
    {
        // $this->em->createQuery('DELETE FROM App\Entity\ProductEdition pe')->execute();
        // $this->em->createQuery('DELETE FROM App\Entity\ProductTitle pt')->execute();

        foreach ($this->em->getRepository(ProductImage::class)->findAll() as $image) {
            $this->em->remove($image);
        }
        $this->em->flush();

        foreach ($this->em->getRepository(ProductEdition::class)->findAll() as $edition) {
            $this->em->remove($edition);
        }
        $this->em->flush();

        $this->em->createQuery('DELETE FROM App\Entity\ProductTitle')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\RecordLabel')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Artist')->execute();

        $this->em->clear();
        $this->em->close();

        unset($this->em, $this->service);
    }

    public function testCreateProductTitle(): void
    {
        $name = 'ProductoTest_' . time();
        $dto = new ProductTitleDto(null, $name, null, null);

        $created = $this->service->create($dto);

        $this->assertNotNull($created->id);
        $this->assertEquals($name, $created->name);

        $saved = $this->em->getRepository(ProductTitle::class)->find($created->id);
        $this->assertInstanceOf(ProductTitle::class, $saved);
        $this->assertEquals($name, $saved->getName());
    }

    public function testCreateDuplicateThrowsBusinessException(): void
    {
        $name = 'ProductoDuplicado_' . rand(1, 1000) . time();
        $dto = new ProductTitleDto(null, $name, null, null);

        $this->service->create($dto);

        $this->expectException(BusinessException::class);

        $this->service->create($dto);
    }

    public function testDeleteProductTitle(): void
    {
        $name = 'ProductoParaEliminar_' . time();
        $created = $this->service->create(new ProductTitleDto(null, $name, null, null));

        $this->service->deleteProductTitle($created->id);

        $deleted = $this->em->getRepository(ProductTitle::class)->find($created->id);
        $this->assertNull($deleted);
    }

    public function testDeleteNonexistentThrowsException(): void
    {
        $this->expectException(BusinessException::class);

        $this->service->deleteProductTitle(9999999);
    }

    public function testGetPaginated(): void
    {
        // Asegurarse de que hay al menos un producto para paginar
        $this->service->create(new ProductTitleDto(null, 'ProductoTestPaginate_' . time(), null, null));

        $filter = new ProductFilterDto(
            page: 1,
            limit: 10
        );

        $pagination = $this->service->getPaginated($filter);

        $this->assertInstanceOf(PaginationInterface::class, $pagination);
        $this->assertLessThanOrEqual(10, count($pagination->getItems()));

        foreach ($pagination->getItems() as $item) {
            $this->assertInstanceOf(ProductTitleDto::class, $item);
            $this->assertNotNull($item->id);
            $this->assertNotEmpty($item->name);
        }
    }

    public function testGetProductTitle(): void
    {
        $name = 'ProductoParaObtener_' . time();
        $created = $this->service->create(new ProductTitleDto(null, $name, null, null));

        $retrievedDto = $this->service->getProductTitle($created->id);

        $this->assertInstanceOf(ProductTitleDto::class, $retrievedDto);
        $this->assertEquals($created->id, $retrievedDto->id);
        $this->assertEquals($name, $retrievedDto->name);
    }

    public function testGetProductTitleReturnsEntityIfRequested(): void
    {
        $name = 'ProductoParaObtenerEntidad_' . time();
        $created = $this->service->create(new ProductTitleDto(null, $name, null, null));

        $entity = $this->service->getProductTitle($created->id, true);

        $this->assertInstanceOf(ProductTitle::class, $entity);
        $this->assertEquals($name, $entity->getName());
    }

    public function testGetProductTitleNotFoundThrowsException(): void
    {
        $this->expectException(BusinessException::class);

        $this->service->getProductTitle(9999999);
    }

    public function testSaveProductTitle(): void
    {
        $name = 'ProductoParaGuardar_' . time();
        $created = $this->service->create(new ProductTitleDto(null, $name, null, null));

        $updatedName = $name . '_Actualizado';
        $dtoToSave = new ProductTitleDto($created->id, $updatedName, null, null);

        $productEntity = $this->em->getRepository(ProductTitle::class)->find($created->id);
        $productEntity->setName($updatedName);

        $this->service->saveProductTitle($dtoToSave);

        $refreshed = $this->em->getRepository(ProductTitle::class)->find($created->id);
        $this->assertEquals($updatedName, $refreshed->getName());
    }

    public function testSaveProductTitleNotFoundThrowsException(): void
    {
        $dto = new ProductTitleDto(9999999, 'ProductoInexistente', null, null);

        $this->expectException(BusinessException::class);

        $this->service->saveProductTitle($dto);
    }
}