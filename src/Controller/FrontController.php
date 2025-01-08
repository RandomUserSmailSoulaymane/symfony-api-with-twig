<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(): Response
    {
        return $this->redirectToRoute('login_page');
    }

    #[Route('/login', name: 'login_page')]
    public function loginPage(): Response
    {
        return $this->render('login.html.twig');
    }

    #[Route('/register', name: 'register_page')]
    public function registerPage(): Response
    {
        return $this->render('register.html.twig');
    }

    #[Route('/articles', name: 'articles_page')]
    public function articlesPage(): Response
    {
        return $this->render('articles/index.html.twig');
    }

    #[Route('/products', name: 'products_page')]
    public function productsPage(): Response
    {
        return $this->render('products/index.html.twig');
    }
}
