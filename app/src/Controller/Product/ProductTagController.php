<?php

namespace App\Controller\Product;

use App\Entity\ProductTag;
use App\DTO\Product\ProductTagDto;
use App\Form\Product\ProductTagForm;
use App\DTO\Product\ProductTagFilterDto;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Product\ProductTagCrudService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/product/product/tag')]
final class ProductTagController extends AbstractController
{
    public function __construct(
        private ProductTagCrudService $productTagCrudService)
    {}

    #[Route(name: 'app_product_product_tag_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filterDto = new ProductTagFilterDto(
            page: $request->query->getInt('page', 1),
            limit: 10
        );

        $pagination = $this->productTagCrudService->getPaginated($filterDto);

        return $this->render('product/product_tag/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_product_product_tag_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $productTag = new ProductTag();
        $form = $this->createForm(ProductTagForm::class, $productTag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productTagDto = new ProductTagDto(
                                    null, 
                                    $productTag->getName()
                                );

            $producttagDto = $this->productTagCrudService->create($productTagDto);

            return $this->redirectToRoute('app_product_product_tag_index', ['id' => $producttagDto->id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/product_tag/new.html.twig', [
            'product_tag' => $productTag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_product_tag_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductTag $productTag): Response
    {
        $form = $this->createForm(ProductTagForm::class, $productTag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $productTagDto = new ProductTagDto($productTag->getId(), $productTag->getName());
            $this->productTagCrudService->save($productTagDto);

            return $this->redirectToRoute('app_product_product_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/product_tag/edit.html.twig', [
            'product_tag' => $productTag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_product_tag_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, ProductTag $productTag): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productTag->getId(), $request->getPayload()->getString('_token'))) {
            
            $this->productTagCrudService->delete($productTag->getId());
            
        }

        return $this->redirectToRoute('app_product_product_tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
