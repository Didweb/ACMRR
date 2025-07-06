<?php
namespace App\Tests\Service\Product;

use App\Entity\Artist;
use App\Entity\RecordLabel;
use App\Entity\ProductImage;
use App\Entity\ProductTitle;
use App\Entity\ProductEdition;
use App\Exception\BusinessException;
use App\DTO\Product\ProductEditionDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Product\ProductEditionCrudService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ProductEditionCrudServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ProductEditionCrudService $service;
    private $dummyProductTitleId;
    private $dummyRecordLabelId;
    private $dummyArtistId;

    protected function setUp(): void
    {
        self::bootKernel();

    $container = static::getContainer();

    $this->em = $container->get(EntityManagerInterface::class);

    $productRepository = $container->get('App\Repository\Product\ProductRepository');
    $productEditionRepository = $container->get('App\Repository\Product\ProductEditionRepository');
    $recordLabelRepository = $container->get('App\Repository\RecordLabel\RecordLabelRepository');
    $artistRepository = $container->get('App\Repository\Artist\ArtistRepository');
    $csrfTokenManager = $container->get(CsrfTokenManagerInterface::class);
    $barcodeGenerator = $container->get('App\Service\Product\BarcodeImageGenerator');

    $productTitle = new ProductTitle();
    $productTitle->setName('Dummy Title');
    $this->em->persist($productTitle);

    $recordLabel = new RecordLabel();
    $recordLabel->setName('Dummy Label');
    $this->em->persist($recordLabel);

    $artist = new Artist();
    $artist->setName('Dummy Artist');
    $this->em->persist($artist);

    $this->em->flush();

    $this->dummyProductTitleId = $productTitle->getId();
    $this->dummyRecordLabelId = $recordLabel->getId();
    $this->dummyArtistId = $artist->getId();

    $this->service = new ProductEditionCrudService(
        $productRepository,
        $productEditionRepository,
        $recordLabelRepository,
        $artistRepository,
        $this->em,
        $csrfTokenManager,
        $barcodeGenerator
    );
    }

    protected function tearDown(): void
    {

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

    public function testCreateProductEdition(): void
    {
        $dto = $this->createValidProductEditionDto();

        $createdDto = $this->service->create($dto);

        $this->assertNotNull($createdDto->id);

        $savedEntity = $this->em->getRepository(ProductEdition::class)->find($createdDto->id);
        $this->assertInstanceOf(ProductEdition::class, $savedEntity);
        $this->assertEquals($dto->year, $savedEntity->getYear());
        // ... más asserts según propiedades
    }

    public function testCreateThrowsExceptionWhenProductNotFound(): void
    {
        $dto = new ProductEditionDto(
            id: null,
            title: ['id' => 99999999, 'name' => 'Non Existing Product'], // ID que no existe
            label: $this->dummyRecordLabelId,
            year: 2024,
            format: 'LP',
            barcode: null,
            stockNew: 5,
            priceNew: 19.99,
            productUsedItems: [],
            artists: [
                ['id' => $this->dummyArtistId]
            ],
            tracks: []
        );

        $this->expectException(BusinessException::class);
        $this->service->create($dto);
    }

    public function testCreateThrowsExceptionWhenLabelNotFound(): void
    {
        $dto = new ProductEditionDto(
            id: null,
            title: ['id' => $this->dummyProductTitleId, 'name' => 'Dummy Title'],
            label: 99999999, // Label que no existe
            year: 2024,
            format: 'LP',
            barcode: null,
            stockNew: 5,
            priceNew: 19.99,
            productUsedItems: [],
            artists: [
                ['id' => $this->dummyArtistId]
            ],
            tracks: []
        );

        $this->expectException(BusinessException::class);
        $this->service->create($dto);
    }

    public function testCreateThrowsExceptionWhenArtistNotFound(): void
    {
        $dto = new ProductEditionDto(
            id: null,
            title: ['id' => $this->dummyProductTitleId, 'name' => 'Dummy Title'],
            label: $this->dummyRecordLabelId,
            year: 2024,
            format: 'LP',
            barcode: null,
            stockNew: 5,
            priceNew: 19.99,
            productUsedItems: [],
            artists: [
                ['id' => 99999999] // Artista que no existe
            ],
            tracks: []
        );

        $this->expectException(BusinessException::class);
        $this->service->create($dto);
    }

    public function testCreateWithInvalidProductThrowsException(): void
    {
        $dto = new ProductEditionDto(
            id: null, 
            title: ['id' => $this->dummyProductTitleId, 'name' => 'Dummy Title'],
            label: $this->dummyRecordLabelId,
            year: 2024,
            format: 'XX',
            barcode: null,
            stockNew: 5,
            priceNew: 19.99,
            productUsedItems: [],
            artists: [
                ['id' => $this->dummyArtistId]
            ],
            tracks: []
        );

        $this->expectException(BusinessException::class);
        $this->service->create($dto);
    }

    public function testDeleteProductEdition(): void
    {
        $dto = $this->createValidProductEditionDto();
        $createdDto = $this->service->create($dto);

        $this->service->delete($createdDto);

        $deleted = $this->em->getRepository(ProductEdition::class)->find($createdDto->id);
        $this->assertNull($deleted);
    }

    public function testDeleteNonexistentThrowsException(): void
    {
            $dto = new ProductEditionDto(
                    id: 999999, 
                    title: ['id' => $this->dummyProductTitleId, 'name' => 'Dummy Title'], 
                    label: $this->dummyRecordLabelId, 
                    year: 2024,
                    format: 'LP', 
                    barcode: null,
                    stockNew: 5,
                    priceNew: 19.99,
                    productUsedItems: [],
                    artists: [
                        ['id' => $this->dummyArtistId]
                    ],
                    tracks: []
                );

        $this->expectException(BusinessException::class);
        $this->service->delete($dto);
    }

    // Métodos auxiliares

    private function createValidProductEditionDto(): ProductEditionDto
    {
        return new ProductEditionDto(
            id: null,
            title: ['id' => $this->dummyProductTitleId, 'name' => 'Dummy Title'], 
            label: $this->dummyRecordLabelId, 
            year: 2024,
            format: 'LP', 
            barcode: null,
            stockNew: 5,
            priceNew: 19.99,
            productUsedItems: [], // necesario
            artists: [
                ['id' => $this->getFirstArtistId()]
            ],
            tracks: []
        );
    }

    private function getFirstArtistId(): int
    {
        $artist = $this->em->getRepository('App\Entity\Artist')->findOneBy([]);
        if (!$artist) {
            throw new \Exception('No hay Artist para tests');
        }
        return $artist->getId();
    }
}