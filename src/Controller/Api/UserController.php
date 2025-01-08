<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    // 1) GET /api/users
    #[Route('', name: 'api_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        // Optionally restrict access to admins only
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $users = $em->getRepository(User::class)->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id'    => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(), // Include roles for admin visibility
            ];
        }

        return $this->json($data);
    }

    // 2) POST /api/users
    #[Route('', name: 'api_user_create', methods: ['POST'])]
public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
{
    $payload = json_decode($request->getContent(), true);

    if (!isset($payload['email'], $payload['password'])) {
        return $this->json(['error' => 'Missing required fields'], 400);
    }

    // Validate email format
    if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
        return $this->json(['error' => 'Invalid email format'], 400);
    }

    // Check if user already exists
    $existing = $em->getRepository(User::class)->findOneBy(['email' => $payload['email']]);
    if ($existing) {
        return $this->json(['error' => 'User already exists'], 400);
    }

    // Create user
    $user = new User();
    $user->setEmail($payload['email']);
    $user->setRoles(['ROLE_USER']);

    // Hash the password
    $hashedPassword = $passwordHasher->hashPassword($user, $payload['password']);
    $user->setPassword($hashedPassword);

    $em->persist($user);
    $em->flush();

    return $this->json([
        'message' => 'User created successfully',
        'id'      => $user->getId(),
    ], 201);
}


    // 3) GET /api/users/{id}
    #[Route('/{id}', name: 'api_user_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $data = [
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        return $this->json($data);
    }

    // 4) PUT /api/users/{id}
    #[Route('/{id}', name: 'api_user_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $payload = json_decode($request->getContent(), true);

        if (isset($payload['email'])) {
            // Validate email format
            if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->json(['error' => 'Invalid email format'], 400);
            }

            // Optional: check if new email is already used by someone else
            $existing = $em->getRepository(User::class)->findOneBy(['email' => $payload['email']]);
            if ($existing && $existing->getId() !== $id) {
                return $this->json(['error' => 'Email is already in use'], 400);
            }

            $user->setEmail($payload['email']);
        }

        if (isset($payload['password'])) {
            // Hash the new password
            $hashedPassword = $passwordHasher->hashPassword($user, $payload['password']);
            $user->setPassword($hashedPassword);
        }

        $em->flush();

        return $this->json([
            'message' => 'User updated successfully',
            'id'      => $user->getId(),
        ]);
    }

    // 5) DELETE /api/users/{id}
    #[Route('/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $em->remove($user);
        $em->flush();

        return $this->json(['message' => 'User deleted successfully']);
    }
}
