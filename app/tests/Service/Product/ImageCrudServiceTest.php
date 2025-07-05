<?php
namespace App\Tests\Service\Product;

use App\Entity\RecordLabel;
use App\Entity\ProductTitle;
use App\Entity\ProductEdition;
use App\DTO\Images\ImageUploadDto;
use App\Exception\BusinessException;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Product\ImageCrudService;
use App\ValueObject\Product\ProductFormat;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageCrudServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private string $uploadDir;
    private string $publicPath;
    private ImageCrudService $service;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = self::getContainer()->get(EntityManagerInterface::class);

        $this->uploadDir = sys_get_temp_dir() . '/img_uploads/';
        $this->publicPath = $this->uploadDir;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $this->service = new ImageCrudService($this->em, $this->uploadDir, $this->publicPath);

        array_map('unlink', glob($this->uploadDir . '*'));
    }

    public function testUploadImageToProductEdition(): void
    {
        
        $title = new ProductTitle();
        $title->setName('Título de prueba');
        $this->em->persist($title);

        $label = new RecordLabel();
        $label->setName('Sello de prueba');
        $this->em->persist($label);

        $format = new ProductFormat('LP'); 

        $edition = new ProductEdition();
        $edition->setTitle($title);
        $edition->setLabel($label);
        $edition->setYear(2020);
        $edition->setFormat($format);
        $edition->setStockNew(10);
        $edition->setPriceNew(99.99);

        $this->em->persist($edition);
        $this->em->flush();

        $file = $this->createTempUploadedFile('test.jpg');

        $dto = new ImageUploadDto($edition->getId(), 'productEdition', $file);

        $response = $this->service->upload($dto);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('last_image', $data['data']);

        $filename = $data['data']['last_image'];
        $this->assertFileExists($this->uploadDir . $filename);

    }

    public function testUploadFailsIfEntityNotFound(): void
    {
        $file = $this->createTempUploadedFile('test.jpg');
        $dto = new ImageUploadDto(999999, 'productEdition', $file); // ID inexistente

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Entidad [App\Entity\ProductEdition] no encontrada');

        $this->service->upload($dto);
    }

    public function testUploadFailsWithInvalidExtension(): void
    {
        $edition = $this->createValidProductEdition();

        $invalidFile = new UploadedFile(
            tempnam(sys_get_temp_dir(), 'test'),
            'test.exe',
            'application/octet-stream',
            null,
            true
        );

        $dto = new ImageUploadDto($edition->getId(), 'productEdition', $invalidFile);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Tipo de archivo no permitido');

        $this->service->upload($dto);
    }


    public function testUploadFailsWithInvalidEntityName(): void
    {
        $file = $this->createTempUploadedFile('test.jpg');
        $dto = new ImageUploadDto(1, 'invalidaEntidad', $file);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Entidad no valida');

        $this->service->upload($dto);
    }


    private function createValidProductEdition(): ProductEdition
    {
        $title = new ProductTitle();
        $title->setName('Test title');
        $this->em->persist($title);

        $label = new RecordLabel();
        $label->setName('Test label');
        $this->em->persist($label);

        $edition = new ProductEdition();
        $edition->setTitle($title);
        $edition->setLabel($label);
        $edition->setYear(2020);
        $edition->setFormat(new ProductFormat('LP'));
        $edition->setStockNew(5);
        $edition->setPriceNew(25.50);

        $this->em->persist($edition);
        $this->em->flush();

        return $edition;
    }

    private function createTempUploadedFile(string $filename): UploadedFile
    {
        $fixturePath = __DIR__ . '/fixtures/' . $filename;
        $tmpPath = $this->uploadDir . $filename;

        copy($fixturePath, $tmpPath);

        return new UploadedFile(
            $tmpPath,
            $filename,
            'image/jpeg',
            null,
            true
        );
    }

}