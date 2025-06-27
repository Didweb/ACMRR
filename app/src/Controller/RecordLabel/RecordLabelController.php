<?php

namespace App\Controller\RecordLabel;

use App\Entity\RecordLabel;
use App\Service\DtoValidator;
use App\DTO\RecordLabel\RecordLabelDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RecordLabel\RecordLabelForm;
use App\DTO\RecordLabel\RecordLabelFilterDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\RecordLabel\RecordLabelCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/record/label')]
final class RecordLabelController extends AbstractController
{
    public function __construct(
        private RecordLabelCrudService $recordLabelCrudService,
        private DtoValidator $dtoValidator)
    {}

    #[Route('/list', name: 'app_record_label_index', methods: ['GET'])]
    public function index(Request $request): Response
    {

        $filterDto = new RecordLabelFilterDto(
            page: $request->query->getInt('page', 1),
            limit: 10
        );

        $pagination = $this->recordLabelCrudService->getPaginated($filterDto);

        return $this->render('record_label/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_record_label_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recordLabel = new RecordLabel();
        $form = $this->createForm(RecordLabelForm::class, $recordLabel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recordLabelDto = RecordLabelDto::fromEntity($recordLabel);
            $this->dtoValidator->validate($recordLabelDto);  

            $recordLabel = $this->recordLabelCrudService->create($recordLabelDto);
            
            $this->addFlash('success', 'Sello creado correctamente.');
            return $this->redirectToRoute('app_record_label_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('record_label/new.html.twig', [
            'record_label' => $recordLabel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_record_label_show', methods: ['GET'])]
    public function show(RecordLabel $recordLabel): Response
    {
        return $this->render('record_label/show.html.twig', [
            'record_label' => $recordLabel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_record_label_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RecordLabel $recordLabel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecordLabelForm::class, $recordLabel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_record_label_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('record_label/edit.html.twig', [
            'record_label' => $recordLabel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_record_label_delete', methods: ['POST'])]
    public function delete(Request $request, RecordLabel $recordLabel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recordLabel->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($recordLabel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_record_label_index', [], Response::HTTP_SEE_OTHER);
    }
}
