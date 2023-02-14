<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class UserController extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/users', name: 'app_user')]
    public function index(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $userIds = $doctrine
            ->getRepository(User::class)
            ->getUserIdsByFilter($request->query->all());
        $wordCount = $doctrine
            ->getRepository(Post::class)
            ->getPostWordCountByUsers($userIds);

        return $this->json([
            'averageWordCount' => $wordCount,
        ]);
    }
}
