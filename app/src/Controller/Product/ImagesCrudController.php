<?php
namespace App\Controller\Product;

use App\DTO\Images\ImageUploadDto;
use App\Service\Product\ImageCrudService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/images/product/crud')]
class ImagesCrudController
{
    public function __construct(
        private ImageCrudService $imageCrudService)
    {}

    #[Route('/upload', name: 'app_product_image_upload', methods: ['POST'])]
    public function uploadImage(Request $request): Response
    {
        $imageUploadDto = new ImageUploadDto(
            entityId: $request->request->get('entityId'),
            entityName: $request->request->get('entityName'),
            image: $request->files->get('image')
        );

        return  $this->imageCrudService->upload($imageUploadDto);
    }

    #[Route('/delete/{id}', name: 'app_product_image_delete', methods: ['DELETE'])]
    public function deleteImage(int $id): Response
    {
      
        return  $this->imageCrudService->delete($id);
    }
}