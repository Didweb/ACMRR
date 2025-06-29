<?php
namespace App\Controller\Dashboard;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dashboard')]
class DasahboardController extends AbstractController
{
    #[Route('/', name: 'app_home_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/home.html.twig');
    }
}