<?php

namespace App\Controller;

use App\Request\GetServersRequest;
use App\UseCase\GetServersUseCase;
use Error;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class ServersController extends AbstractController
{

    public function __construct(
        protected readonly GetServersUseCase $getServersUseCase,
        protected readonly LoggerInterface  $logger
    )
    {
    }


    #[Route('/servers', name: 'servers', methods: ['GET'], format: 'json')]
    public function index(
        Request $request
    ): JsonResponse
    {
        try {
            $response = $this->getServersUseCase->handle(
                GetServersRequest::fromRequest($request)
            );

            return new JsonResponse($response, $response->status);

        }
        catch (Exception $e) {
            $this->logger->error("Failed to fetch server list: {$e->getMessage()}", ['exception' => $e]);
            return $this->json("Unable to fetch server list. Please try again later", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
