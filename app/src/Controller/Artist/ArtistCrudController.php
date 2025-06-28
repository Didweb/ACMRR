<?php

namespace App\Controller\Artist;

use App\Entity\Artist;
use App\DTO\Artist\ArtistDto;
use App\Service\DtoValidator;
use App\Form\Artist\ArtistForm;
use App\DTO\Artist\ArtistFilterDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Artist\ArtistCrudService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/artist')]
final class ArtistCrudController extends AbstractController
{

    public function __construct(
        private ArtistCrudService $artistCrudService,
        private DtoValidator $dtoValidator)
    {}

    #[Route('/list', name: 'app_artist_crud_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filterDto = new ArtistFilterDto(
            page: $request->query->getInt('page', 1),
            limit: 10
        );

         $pagination = $this->artistCrudService->getPaginated($filterDto);

        return $this->render('artist_crud/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_artist_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistForm::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $artistDto = new ArtistDto(null, $artist->getName());

            $artist = $this->artistCrudService->create($artistDto);

            return $this->redirectToRoute('app_artist_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist_crud/new.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_artist_crud_show', methods: ['GET'])]
    public function show(Artist $artist): Response
    {
        return $this->render('artist_crud/show.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_artist_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArtistForm::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_artist_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist_crud/edit.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_artist_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($artist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_artist_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
