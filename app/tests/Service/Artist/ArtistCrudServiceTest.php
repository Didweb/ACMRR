<?php
namespace App\Tests\Service\Artist;

use App\Entity\Artist;
use App\DTO\Artist\ArtistDto;
use PHPUnit\Framework\TestCase;
use App\DTO\Artist\ArtistFilterDto;
use App\Exception\BusinessException;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Artist\ArtistCrudService;
use App\Repository\Artist\ArtistRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ArtistCrudServiceTest extends TestCase
{
    private $artistRepository;
    private $paginator;
    private $em;
    private $csrfTokenManager;
    private $service;

    protected function setUp(): void
    {
        $this->artistRepository = $this->createMock(ArtistRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);

        $this->service = new ArtistCrudService(
            $this->artistRepository,
            $this->paginator,
            $this->em,
            $this->csrfTokenManager
        );
    }

    public function testGetPaginatedReturnsPaginationWithArtistDtos()
    {
        $filter = new ArtistFilterDto(page: 1, limit: 10);
   
        $artist1 = new Artist();
        $artist1->setName('Artist 1');
        $reflection = new \ReflectionClass($artist1);
        $idProp = $reflection->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($artist1, 1);

        $artist2 = new Artist();
        $artist2->setName('Artist 2');
        $idProp->setValue($artist2, 2);

        $paginationMock = $this->createMock(PaginationInterface::class);
        $paginationMock->expects($this->once())->method('getItems')->willReturn([$artist1, $artist2]);
        $paginationMock->expects($this->once())->method('setItems')->with($this->callback(function ($items) {
            return count($items) === 2 && $items[0] instanceof ArtistDto;
        }));

        $queryBuilderMock = $this->getMockBuilder(\Doctrine\ORM\QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->artistRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilderMock);

        $this->paginator->expects($this->once())
            ->method('paginate')
            ->with($queryBuilderMock, 1, 10)
            ->willReturn($paginationMock);

        $result = $this->service->getPaginated($filter);

        $this->assertSame($paginationMock, $result);
    }

    public function testCreateThrowsExceptionIfArtistExists()
    {
        $artistDto = new ArtistDto(null, 'Existing Artist');

        $existingArtist = new Artist();
        $existingArtist->setName('Existing Artist');

        $this->artistRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Existing Artist'])
            ->willReturn($existingArtist);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Error al crear artista. Nombre Duplicado');

        $this->service->create($artistDto);
    }

    public function testCreatePersistsAndReturnsArtistDto()
    {
        $artistDto = new ArtistDto(null, 'New Artist');

        $this->artistRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'New Artist'])
            ->willReturn(null);

        $persistedArtist = null;
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($artist) use (&$persistedArtist) {
                $persistedArtist = $artist;
                return $artist instanceof Artist;
            }));

        $this->em->expects($this->once())
            ->method('flush')
            ->willReturnCallback(function() use (&$persistedArtist) {
                $reflection = new \ReflectionClass($persistedArtist);
                $idProp = $reflection->getProperty('id');
                $idProp->setAccessible(true);
                $idProp->setValue($persistedArtist, 123);
            });

        $result = $this->service->create($artistDto);

        $this->assertInstanceOf(ArtistDto::class, $result);
        $this->assertEquals(123, $result->id);
        $this->assertEquals('New Artist', $result->name);
    }

    public function testSaveThrowsExceptionIfArtistNotFound()
    {
        $artistDto = new ArtistDto(999, 'Name');

        $this->artistRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('No existe el artista.');

        $this->service->save($artistDto);
    }

    public function testSaveUpdatesAndFlushes()
    {
        $artistDto = new ArtistDto(1, 'Updated Name');

        $artist = new Artist();
        $artist->setName('Old Name');
        $reflection = new \ReflectionClass($artist);
        $idProp = $reflection->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($artist, 1);

        $this->artistRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($artist);

        $this->em->expects($this->once())
            ->method('flush');

        $result = $this->service->save($artistDto);

        $this->assertEquals('Updated Name', $artist->getName());
        $this->assertInstanceOf(ArtistDto::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Updated Name', $result->name);
    }

    public function testDeleteThrowsExceptionIfArtistNotFound()
    {
        $this->artistRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('No existe el artista.');

        $this->service->delete(999);
    }

    public function testDeleteRemovesAndFlushes()
    {
        $artist = new Artist();
        $artist->setName('ToDelete');
        $reflection = new \ReflectionClass($artist);
        $idProp = $reflection->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($artist, 5);

        $this->artistRepository->expects($this->once())
            ->method('find')
            ->with(5)
            ->willReturn($artist);

        $this->em->expects($this->once())
            ->method('remove')
            ->with($artist);

        $this->em->expects($this->once())
            ->method('flush');

        $this->service->delete(5);

        $this->assertTrue(true);
    }

}