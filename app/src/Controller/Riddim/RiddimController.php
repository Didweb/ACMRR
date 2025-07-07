<?php

namespace App\Controller\Riddim;

use App\DTO\Riddim\RiddimDto;
use App\Entity\Riddim;
use App\Service\DtoValidator;
use App\Form\Riddim\RiddimForm;
use App\DTO\Riddim\RiddimFilterDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Riddim\RiddimCrudService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/riddim')]
final class RiddimController extends AbstractController
{
    public function __construct(
        private RiddimCrudService $riddimCrudService,
        private DtoValidator $dtoValidator)
    {}

    #[Route(name: 'app_riddim_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filterDto = new RiddimFilterDto(
            page: $request->query->getInt('page', 1),
            limit: 10
        );

        $this->dtoValidator->validate($filterDto);

        $pagination = $this->riddimCrudService->getPaginated($filterDto);

        return $this->render('riddim/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_riddim_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $riddim = new Riddim();
        $form = $this->createForm(RiddimForm::class, $riddim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $riddimDto = new RiddimDto(null, $riddim->getName(), []);
            $riddim = $this->riddimCrudService->create($riddimDto);

            return $this->redirectToRoute('app_riddim_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('riddim/new.html.twig', [
            'riddim' => $riddim,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_riddim_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Riddim $riddim, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RiddimForm::class, $riddim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_riddim_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('riddim/edit.html.twig', [
            'riddim' => $riddim,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_riddim_delete', methods: ['POST'])]
    public function delete(Request $request, Riddim $riddim, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$riddim->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($riddim);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_riddim_index', [], Response::HTTP_SEE_OTHER);
    }
}
