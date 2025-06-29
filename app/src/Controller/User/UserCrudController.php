<?php

namespace App\Controller\User;

use App\Entity\User;
use App\DTO\User\UserDto;
use App\Form\User\UserForm;
use App\Service\DtoValidator;
use App\DTO\User\UserDeleteDto;
use App\DTO\User\UserFilterDto;
use App\Service\User\UserCrudService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/user/crud')]
final class UserCrudController extends AbstractController
{
    public function __construct(
        private UserCrudService $userCrudService,
        private DtoValidator $dtoValidator)
    {}

    #[Route('/list', name: 'app_user_crud_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filterDto = new UserFilterDto(
            page: $request->query->getInt('page', 1),
            limit: 10
        );

        $pagination = $this->userCrudService->getPaginated($filterDto);
      
        return $this->render('user_crud/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_user_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $userDto = UserDto::fromEntity($user);

            $this->dtoValidator->validate($userDto);  

            $user = $this->userCrudService->create($userDto);
            
            $this->addFlash('success', 'Usuario creado correctamente.');
            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_crud/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_crud_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user_crud/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
             $this->addFlash('success', 'Usuario editado correctamente.');
            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_crud/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_crud_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $id = $request->attributes->get('id');
        $userDeletedto = new UserDeleteDto(
                        $id,
                        $request->getPayload()->getString('_token')
                    );

        $this->dtoValidator->validate($userDeletedto);    

        $result = $this->userCrudService->delete($userDeletedto);

         if (!$result->success) {
            $this->addFlash('error', $result->message);
        } else {
            $this->addFlash('success', 'Usuario eliminado correctamente.');
        }

        return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
