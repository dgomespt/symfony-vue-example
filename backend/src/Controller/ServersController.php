<?php

namespace App\Controller;

use App\UseCase\GetServersUseCase;
use Error;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServersController extends AbstractController
{

    public function __construct(
        protected readonly GetServersUseCase $getServersUseCase,
        protected readonly LoggerInterface  $logger
    )
    {
    }


    #[Route('/servers', name: 'servers')]
    public function index(Request $request): JsonResponse
    {
        try {
            $response = $this->getServersUseCase->handle(
                $request->get('page', 1),
                $request->get('itemsPerPage', 10),
                $request->get('filters', [])
                , $request->get('order'));

            $response = new JsonResponse($response->toArray());

            return $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );
        }
        catch(Error $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        catch (Exception $e) {
            $this->logger->error("Failed to fetch server list: {$e->getMessage()}", ['exception' => $e]);
            return $this->json("Unable to fetch server list. Please try again later", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
