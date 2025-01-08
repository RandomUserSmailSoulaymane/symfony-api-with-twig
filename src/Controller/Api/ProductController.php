<?php

namespace App\Controller\Api;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    #[Route('', name: 'products_list', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $products = $em->getRepository(Product::class)->findAll();
        $data = [];
        foreach ($products as $p) {
            $data[] = [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'price' => $p->getPrice(),
            ];
        }
        return $this->json($data);
    }

    #[Route('', name: 'products_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['name'], $content['price'])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $product = new Product();
        $product->setName($content['name']);
        $product->setPrice($content['price']);

        $em->persist($product);
        $em->flush();

        return $this->json(['message' => 'Product created', 'id' => $product->getId()], 201);
    }

    #[Route('/{id}', name: 'products_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice()
        ]);
    }

    #[Route('/{id}', name: 'products_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);
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
        return $this->json(['message' => 'Product updated', 'id' => $product->getId()]);
    }

    #[Route('/{id}', name: 'products_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(['message' => 'Product deleted']);
    }
}
