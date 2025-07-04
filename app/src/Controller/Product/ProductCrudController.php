<?php

namespace App\Controller\Product;

use App\Entity\ProductTitle;
use App\Service\DtoValidator;
use App\DTO\Product\ProductFilterDto;
use App\DTO\Product\ProductTitleDto;
use App\Form\Product\ProductTitleForm;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Product\ProductCrudService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/product/crud')]
final class ProductCrudController extends AbstractController
{
    public function __construct(
        private ProductCrudService $productCrudService,
        private DtoValidator $dtoValidator)
    {}

    #[Route('/list', name: 'app_product_crud_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filterDto = new ProductFilterDto(
            page: $request->query->getInt('page', 1),
            limit: 10
        );
        
        $pagination = $this->productCrudService->getPaginated($filterDto);

        return $this->render('product/product_crud/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_product_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $productTitle = new ProductTitle();
        $form = $this->createForm(ProductTitleForm::class, $productTitle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productTitleDto = new ProductTitleDto(
                null, 
                $productTitle->getName(),
                null,
                null,
            );
            $productTitleDto = $this->productCrudService->create($productTitleDto);

            return $this->redirectToRoute('app_product_crud_edit', ['id' => $productTitleDto->id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/product_crud/new.html.twig', [
            'product_title' => $productTitle,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_product_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductTitle $productTitle): Response
    {
        $form = $this->createForm(ProductTitleForm::class, $productTitle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $productDto = ProductTitleDto::fromEntity($form->getData());
            $this->productCrudService->saveProductTitle($productDto);

            return $this->redirectToRoute('app_product_crud_index', [], Response::HTTP_SEE_OTHER);
        }
       
        $productTitle = $this->productCrudService->getProductTitleComplete($productTitle->getId());
       
        return $this->render('product/product_crud/edit.html.twig', [
            'productTitle' => $productTitle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_crud_delete', methods: ['POST'])]
    public function delete(Request $request, ProductTitle $productTitle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productTitle->getId(), $request->getPayload()->getString('_token'))) {
            $this->productCrudService->deleteProductTitle($productTitle->getId());

        }

        return $this->redirectToRoute('app_product_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
