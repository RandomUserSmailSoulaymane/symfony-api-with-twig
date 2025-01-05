<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/products')]
class ApiController extends AbstractController
{
    /**
     * GET /api/products
     * Returns JSON array of all products
     */
    #[Route('', name: 'api_products_list', methods: ['GET'])]
    public function list(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id'    => $product->getId(),
                'name'  => $product->getName(),
                'price' => $product->getPrice(),
            ];
        }

        return $this->json($data);
    }

    /**
     * GET /api/products/{id}
     * Returns one product by ID
     */
    #[Route('/{id}', name: 'api_products_show', methods: ['GET'])]
    public function show(ProductRepository $productRepository, int $id): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $data = [
            'id'    => $product->getId(),
            'name'  => $product->getName(),
            'price' => $product->getPrice(),
        ];

        return $this->json($data);
    }

    /**
     * POST /api/products
     * Creates a new product
     * Expects JSON like: {"name": "Example", "price": "15.99"}
     */
    #[Route('', name: 'api_products_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        // Basic error check
        if (!isset($content['name'], $content['price'])) {
            return $this->json(['error' => 'Missing name or price'], 400);
        }

        $product = new Product();
        $product->setName($content['name']);
        $product->setPrice($content['price']);

        $em->persist($product);
        $em->flush();

        return $this->json([
            'message' => 'Product created',
            'id'      => $product->getId()
        ], 201); // 201 = Created
    }

    /**
     * PUT or PATCH /api/products/{id}
     * Updates an existing product
     * Expects JSON like: {"name": "New name", "price": "20.00"}
     */
    #[Route('/{id}', name: 'api_products_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, EntityManagerInterface $em, ProductRepository $productRepository, int $id): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $content = json_decode($request->getContent(), true);

        
        if (isset($content['name'])) {
            $product->setName($content['name']);
        }
        if (isset($content['price'])) {
            $product->setPrice($content['price']);
        }

        $em->flush();

        return $this->json([
            'message' => 'Product updated',
            'id'      => $product->getId()
        ]);
    }

    /**
     * DELETE /api/products/{id}
     * Deletes a product
     */
    #[Route('/{id}', name: 'api_products_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(['message' => 'Product deleted']);
    }
}
