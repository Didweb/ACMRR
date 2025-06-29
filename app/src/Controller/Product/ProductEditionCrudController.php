<?php
namespace App\Controller\Product;

use App\Entity\ProductTitle;
use App\Entity\ProductEdition;
use App\DTO\Product\ProductEditionDto;
use App\Exception\BusinessException;
use App\Form\Product\ProductEditionForm;
use App\Service\Product\ProductCrudService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\Product\ProductEditionCrudService;
use App\Utils\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/product/crud/edition')]
class ProductEditionCrudController extends AbstractController
{
    public function __construct(
        private ProductEditionCrudService $productEditCrudService,
        private ProductCrudService $productCrudService,
        )
    {}

    #[Route('/new/{titleId}', name: 'app_product_edition_crud_new', methods: ['GET', 'POST'])]
    public function new(int $titleId): Response
    {
        $productTitle = $this->productCrudService->getProductTitle($titleId, true);
        $productEdition = new ProductEdition();
        $productEdition->setTitle($productTitle);

        $form = $this->createForm(ProductEditionForm::class, $productEdition);

        return $this->render('product/product_crud/product_edition/_form.html.twig', [
            'formEdition' => $form->createView(),
            'isEdit' => null
        ]);
    }

    #[Route('/save', name: 'app_product_edition_crud_save', methods: ['POST'])]
    public function save(Request $request): Response
    {
        
        $productEdition = new ProductEdition();
        $form = $this->createForm(ProductEditionForm::class, $productEdition);
        $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

            $productEditionDto = ProductEditionDto::fromEntity($productEdition);
            $productTitleDto = $this->productEditCrudService->create($productEditionDto);

            return $this->redirectToRoute('app_product_crud_edit', ['id' => $productTitleDto->title['id']]);
        }

        throw new BusinessException('El formulario no fue enviado correctamente o contiene errores.');
    }

    #[Route('/edit/{id}', name: 'app_product_edition_crud_edit', methods: ['GET','POST'])]
    public function upload(int $id, Request $request): Response
    {
        $productEdition = $this->productEditCrudService->findProductEdition($id);
        $form = $this->createForm(ProductEditionForm::class, $productEdition);
        $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

            $productEditionDto = ProductEditionDto::fromEntity($productEdition);
            $productTitleDto = $this->productEditCrudService->update($productEditionDto);

            return $this->redirectToRoute('app_product_crud_edit', ['id' => $productTitleDto->title['id']]);
        }

        $isEdit = $productEdition->getId() !== null;

        return $this->render('product/product_crud/product_edition/_form.html.twig', [
            'formEdition' => $form->createView(),
            'isEdit' => $isEdit,
            'productEdition' => $productEdition
        ]);
    }


    #[Route('/delete/{id}', name: 'app_product_edition_crud_delete', methods: ['DELETE'])]
    public function delete(int $id, Request $request): Response
    {
        $productEdition = $this->productEditCrudService->findProductEdition($id);
        
        $productEditionDto = ProductEditionDto::fromEntity($productEdition);

        $this->productEditCrudService->delete($productEditionDto);

        return JsonResponseFactory::success(['id' => $productEdition->getTitle()->getId()]);
    }
}