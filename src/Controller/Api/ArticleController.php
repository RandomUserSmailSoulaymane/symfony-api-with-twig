<?php

namespace App\Controller\Api;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/articles')]
class ArticleController extends AbstractController
{
    // 1) GET /api/articles
    #[Route('', name: 'api_article_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $articles = $em->getRepository(Article::class)->findAll();
        $data = [];

        foreach ($articles as $article) {
            $data[] = [
                'id'        => $article->getId(),
                'title'     => $article->getTitle(),
                'content'   => $article->getContent(),
                // Format date/time if not null
                'createdAt' => $article->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updatedAt' => $article->getUpdatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    // 2) POST /api/articles
    #[Route('', name: 'api_article_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        // Validate basic fields
        if (!isset($payload['title'], $payload['content'])) {
            return $this->json(['error' => 'Missing required fields: title, content'], 400);
        }

        // Create a new Article
        $article = new Article();
        $article->setTitle($payload['title']);
        $article->setContent($payload['content']);
        // No manual setCreatedAt/setUpdatedAt - done by lifecycle callbacks

        $em->persist($article);
        $em->flush();

        return $this->json([
            'message' => 'Article created',
            'id'      => $article->getId(),
        ], 201);
    }

    // 3) GET /api/articles/{id}
    #[Route('/{id}', name: 'api_article_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $article = $em->getRepository(Article::class)->find($id);
        if (!$article) {
            return $this->json(['error' => 'Article not found'], 404);
        }

        $data = [
            'id'        => $article->getId(),
            'title'     => $article->getTitle(),
            'content'   => $article->getContent(),
            'createdAt' => $article->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $article->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];

        return $this->json($data);
    }

    // 4) PUT/PATCH /api/articles/{id}
    #[Route('/{id}', name: 'api_article_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $article = $em->getRepository(Article::class)->find($id);
        if (!$article) {
            return $this->json(['error' => 'Article not found'], 404);
        }

        $payload = json_decode($request->getContent(), true);

        if (isset($payload['title'])) {
            $article->setTitle($payload['title']);
        }
        if (isset($payload['content'])) {
            $article->setContent($payload['content']);
        }

        // No manual setUpdatedAt - it's handled by lifecycle callback (PreUpdate)
        $em->flush();

        return $this->json([
            'message' => 'Article updated',
            'id'      => $article->getId(),
        ]);
    }

    // 5) DELETE /api/articles/{id}
    #[Route('/{id}', name: 'api_article_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $article = $em->getRepository(Article::class)->find($id);
        if (!$article) {
            return $this->json(['error' => 'Article not found'], 404);
        }

        $em->remove($article);
        $em->flush();

        return $this->json(['message' => 'Article deleted']);
    }
}
