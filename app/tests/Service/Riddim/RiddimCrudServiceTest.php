<?php
namespace App\Tests\Service\Riddim;

use App\DTO\Riddim\RiddimDto;
use PHPUnit\Framework\TestCase;
use App\DTO\Riddim\RiddimFilterDto;
use App\Exception\BusinessException;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Riddim\RiddimCrudService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RiddimCrudServiceTest extends KernelTestCase
{
    private RiddimCrudService $service;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->service = $container->get(RiddimCrudService::class);
        $this->em = $container->get(EntityManagerInterface::class);

        $connection = $this->em->getConnection();
        $connection->executeStatement('DELETE FROM track');
        $connection->executeStatement('DELETE FROM riddim');
    }

    public function testCreateSuccess(): void
    {
        $dto = new RiddimDto(null, 'Dancehall Fever', []);
        $result = $this->service->create($dto);

        $this->assertInstanceOf(RiddimDto::class, $result);
        $this->assertEquals('Dancehall Fever', $result->name);
        $this->assertNotNull($result->id);
    }

    public function testCreateDuplicateThrows(): void
    {
        $dto = new RiddimDto(null, 'Dub Alert', []);
        $this->service->create($dto);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Nombre Duplicado');
        $this->service->create($dto);
    }

    public function testSaveSuccess(): void
    {
        $created = $this->service->create(new RiddimDto(null, 'Steppa Warrior', []));

        $modified = new RiddimDto(
            id: $created->id,
            name: 'Steppa Revised',
            tracks: $created->tracks
        );

        $updated = $this->service->save($modified);

        $this->assertEquals('Steppa Revised', $updated->name);
    }

    public function testSaveNotFoundThrows(): void
    {
        $dto = new RiddimDto(id: 999, name: 'Ghost Update', tracks: []);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('No existe el riddim.');
        $this->service->save($dto);
    }

    public function testDeleteSuccess(): void
    {
        $dto = $this->service->create(new RiddimDto(null, 'Delete This', []));

        $this->service->delete($dto->id);

        $this->expectException(BusinessException::class);
        $this->service->delete($dto->id); 
    }

    public function testDeleteNotFoundThrows(): void
    {
        $this->expectException(BusinessException::class);
        $this->service->delete(9999);
    }

    public function testGetPaginated(): void
    {
        $this->service->create(new RiddimDto(null, 'Paginate One', []));
        $this->service->create(new RiddimDto(null, 'Paginate Two', []));

        $pagination = $this->service->getPaginated(new RiddimFilterDto(page: 1, limit: 10));

        $this->assertGreaterThanOrEqual(2, $pagination->getTotalItemCount());
        $this->assertIsArray($pagination->getItems());
        $this->assertInstanceOf(RiddimDto::class, $pagination->getItems()[0]);
    }
}