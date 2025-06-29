<?php
namespace App\Service\Product;

use App\Entity\ProductImage;
use App\Entity\ProductEdition;
use App\Entity\ProductUsedItem;
use App\DTO\Images\ImageUploadDto;
use App\Exception\BusinessException;
use App\Utils\JsonResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ImageCrudService
{
    public function __construct(
        private EntityManagerInterface $em,
        private string $imgProductsDir,
        private string $imgProductsPublic,
        ) 
    {}
    
    public function upload(ImageUploadDto $imageUploadDto): Response
    {

        $entityClass = match($imageUploadDto->entityName) {
            'productEdition' => ProductEdition::class,
            'productUsedItem' => ProductUsedItem::class,
            default => throw new BusinessException('Entidad no valida. ['.$imageUploadDto->entityName.']')
        };

        $this->imgProductsDir = rtrim($this->imgProductsDir, '/');

        $entity = $this->em->getRepository($entityClass)->find($imageUploadDto->entityId);

        if (!$entity) {
            throw new BusinessException('Entidad ['.$entityClass.'] no encontrada. '.$entityClass.' '.$imageUploadDto->entityId);
        }

        $ext = $imageUploadDto->image->guessExtension();
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            throw new BusinessException("Tipo de archivo no permitido");
        }

        $filename = 'img_p_'.uniqid() . '.' . $ext;
        $imageUploadDto->image->move($this->imgProductsDir, $filename);

        $image = new ProductImage($filename, $this->imgProductsPublic.$filename);

        if ($entity instanceof ProductEdition) {
            $image->setProductEdition($entity);
        } elseif ($entity instanceof ProductUsedItem) {
            $image->setProductUsedItem($entity);
        }
       
        $this->em->persist($image);
        $this->em->flush();

        $entity = $this->em->getRepository($entityClass)->find($imageUploadDto->entityId);



        return JsonResponseFactory::success(['last_image' => $filename, 'all_images' => $entity->getImagesArray()]);
    }

    
    public function delete(int $id): Response
    {
        $entity = $this->em->getRepository(ProductImage::class)->find($id);

         if (!$entity) {
            throw new BusinessException('Imagen no encontrada. Id: '.$id);
        }

        $imagePath = $entity->getPath();
        $fullPath = $this->imgProductsPublic . $imagePath;

        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
        }

        try{
            $this->em->remove($entity);
            $this->em->flush();

        } catch(Exception $e) {
            throw new BusinessException('Error en la persistencia. '.$e->getMessage());
        }
        
        return JsonResponseFactory::success('Imagen Eliminada');
    }
}