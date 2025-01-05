<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'products_list', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        // Fetch all products from the DB
        $products = $em->getRepository(Product::class)->findAll();

        // Render and pass 'products' to Twig
        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }
}
