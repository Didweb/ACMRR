<?php
namespace App\Tests\Service\Product;

use App\Entity\ProductTag;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use App\DTO\Product\ProductTagDto;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\SchemaTool;
use App\Exception\BusinessException;
use App\DTO\Product\ProductTagFilterDto;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\Product\ProductTagCrudService;
use App\Repository\Product\ProductTagRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTagCrudServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ProductTagCrudService $service;
    private PaginatorInterface $paginator;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $registry = $container->get('doctrine'); // ✅ esto es ManagerRegistry
        $paginator = $container->get(PaginatorInterface::class);

        $productTagRepository = new ProductTagRepository($registry);

        $this->service = new ProductTagCrudService(
            $productTagRepository,
            $paginator,
            $this->em
        );
    }

    protected function tearDown(): void
    {
        $this->em->createQuery('DELETE FROM App\Entity\ProductTag pt')
            ->execute();

        $this->em->clear();
        $this->em->close();
        
        unset($this->em, $this->service, $this->paginator);
    }

    public function testCreateProductTag(): void
    {
        $name = 'TagTestCreate'.time();
        $dto = new ProductTagDto(null, $name);

        $created = $this->service->create($dto);

        $this->assertNotNull($created->id);
        $this->assertEquals($name, $created->name);

        $saved = $this->em->getRepository(ProductTag::class)->find($created->id);
        $this->assertInstanceOf(ProductTag::class, $saved);
        $this->assertEquals($name, $saved->getName());
    }

    public function testCreateDuplicateThrowsBusinessException(): void
    {
        $name = 'TagTestCreate'.rand(1,1000).time();
        $dto = new ProductTagDto(null, $name);

        $this->service->create($dto);

        $this->expectException(BusinessException::class);

        $this->service->create($dto);
    }

    public function testGetPaginated(): void
    {
        $filter = new ProductTagFilterDto(
            page: 1,
            limit: 10
        );

        $pagination = $this->service->getPaginated($filter);

        $this->assertInstanceOf(PaginationInterface::class, $pagination);
        $this->assertLessThanOrEqual(10, count($pagination->getItems()));

        foreach ($pagination->getItems() as $item) {
            $this->assertInstanceOf(ProductTagDto::class, $item);
            $this->assertNotNull($item->id);
            $this->assertNotEmpty($item->name);
        }
    }

    public function testSaveExistingTag(): void
    {
        $name = 'TagToUpdate'.rand(1,1000).time();
        $created = $this->service->create(new ProductTagDto(null, $name));

        // En el servicio save no actualizas campos, si quieres actualizar,
        // tienes que modificar el método save para aplicar cambios antes de persistir.

        // Aquí solo guardamos sin cambio real
        $dto = new ProductTagDto($created->id, $name);

        $this->service->save($dto);

        $refreshed = $this->em->getRepository(ProductTag::class)->find($created->id);
        $this->assertEquals($name, $refreshed->getName());
    }

    public function testSaveNonexistentThrowsException(): void
    {
        $dto = new ProductTagDto(9999999, 'NoExiste');

        $this->expectException(BusinessException::class);

        $this->service->save($dto);
    }

    public function testDeleteExistingTag(): void
    {
        $name = 'TagToDelete'.rand(1,1000).time();
        $created = $this->service->create(new ProductTagDto(null, $name));

        $this->service->delete($created->id);

        $deleted = $this->em->getRepository(ProductTag::class)->find($created->id);
        $this->assertNull($deleted);
    }

    public function testDeleteNonexistentThrowsException(): void
    {
        $this->expectException(BusinessException::class);

        $this->service->delete(9999999);
    }
}