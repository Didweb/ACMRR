<?php
namespace App\Tests\Service\Product;

use App\Entity\RecordLabel;
use App\Exception\BusinessException;
use App\DTO\RecordLabel\RecordLabelDto;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\DTO\RecordLabel\RecordLabelFilterDto;
use App\Service\RecordLabel\RecordLabelCrudService;
use App\Repository\RecordLabel\RecordLabelRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecordLabelCrudServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private RecordLabelCrudService $service;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $registry = $container->get('doctrine');
        $paginator = $container->get(PaginatorInterface::class);

        $recordLabelRepository = new RecordLabelRepository($registry);

        $this->service = new RecordLabelCrudService(
            $recordLabelRepository,
            $paginator,
            $this->em
        );
    }

    protected function tearDown(): void
    {
        $this->em->createQuery('DELETE FROM App\Entity\ProductImage pi')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\ProductEdition pe')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\RecordLabel rl')->execute();

        $this->em->clear();
        $this->em->close();

        unset($this->em, $this->service);
    }

    public function testCreateRecordLabel(): void
    {
        $name = 'LabelTestCreate_' . time();
        $dto = new RecordLabelDto(null, $name);

        $created = $this->service->create($dto);

        $this->assertNotNull($created->id);
        $this->assertEquals($name, $created->name);

        $saved = $this->em->getRepository(RecordLabel::class)->find($created->id);
        $this->assertInstanceOf(RecordLabel::class, $saved);
        $this->assertEquals($name, $saved->getName());
    }

    public function testCreateDuplicateThrowsBusinessException(): void
    {
        $name = 'LabelTestDuplicate_' . rand(1, 1000) . time();
        $dto = new RecordLabelDto(null, $name);

        $this->service->create($dto);

        $this->expectException(BusinessException::class);

        $this->service->create($dto);
    }

    public function testGetPaginated(): void
    {
        // Asegurarse de que hay al menos un registro
        $this->service->create(new RecordLabelDto(null, 'LabelTestPaginate_' . time()));

        $filter = new RecordLabelFilterDto(
            page: 1,
            limit: 10
        );

        $pagination = $this->service->getPaginated($filter);

        $this->assertInstanceOf(PaginationInterface::class, $pagination);
        $this->assertLessThanOrEqual(10, count($pagination->getItems()));

        foreach ($pagination->getItems() as $item) {
            $this->assertInstanceOf(RecordLabelDto::class, $item);
            $this->assertNotNull($item->id);
            $this->assertNotEmpty($item->name);
        }
    }

    public function testSaveExistingLabel(): void
    {
        $name = 'LabelToSave_' . rand(1, 1000) . time();
        $created = $this->service->create(new RecordLabelDto(null, $name));

        $dto = new RecordLabelDto($created->id, $name);

        $this->service->save($dto);

        $refreshed = $this->em->getRepository(RecordLabel::class)->find($created->id);
        $this->assertEquals($name, $refreshed->getName());
    }

    public function testSaveNonexistentThrowsException(): void
    {
        $dto = new RecordLabelDto(9999999, 'NonExistentLabel');

        $this->expectException(BusinessException::class);

        $this->service->save($dto);
    }

    public function testDeleteExistingLabel(): void
    {
        $name = 'LabelToDelete_' . rand(1, 1000) . time();
        $created = $this->service->create(new RecordLabelDto(null, $name));

        $this->service->delete($created->id);

        $deleted = $this->em->getRepository(RecordLabel::class)->find($created->id);
        $this->assertNull($deleted);
    }

    public function testDeleteNonexistentThrowsException(): void
    {
        $this->expectException(BusinessException::class);

        $this->service->delete(9999999);
    }
}