<?php

namespace App\Controller;

use App\Repository\ServerRepository;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServersController extends AbstractController
{

    public function __construct(
        protected readonly ServerRepository $serverRepository,
        protected readonly LoggerInterface  $logger
    )
    {
    }


    #[Route('/servers', name: 'servers')]
    public function index(): JsonResponse
    {
        try {
            $servers = $this->serverRepository->all();
            return $this->json([
                'meta' => [
                    'total' => $servers->count()
                ],
                'data' => $servers->toArray()
            ], Response::HTTP_OK);
        } catch (InvalidArgumentException|Exception $e) {
            $this->logger->error("Failed to fetch server list: {$e->getMessage()}", ['exception' => $e]);
            return $this->json("Unable to fetch server list", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }
}
