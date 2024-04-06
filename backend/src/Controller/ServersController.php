<?php

namespace App\Controller;

use App\Interface\RepositoryInterface;
use App\Repository\ServerCollection;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServersController extends AbstractController
{

    public function __construct(
        protected readonly RepositoryInterface $serverRepository,
        protected readonly LoggerInterface  $logger
    )
    {
    }


    #[Route('/servers', name: 'servers')]
    public function index(Request $request): JsonResponse
    {

        $page = $request->get('page', 1);
        $itemsPerPage = $request->get('itemsPerPage', 10);
        $start = ($page - 1) * $itemsPerPage;

        try {
            $allServers = $this->serverRepository->all();

            $filters = $request->get('filters', []);

            foreach($filters as $name => $value){
                $allServers = $allServers->filter(function($server) use ($name, $value) {
                    return $server->{'get'.ucfirst($name)}() == $value;
                });
            }

            foreach($request->get('order', []) as $name => $direction) {
                $allServers = $allServers->sort($name, $direction);
            }

            $servers = new ServerCollection($allServers->slice($start, $itemsPerPage));

            $response = new JsonResponse([
                'meta' => [
                    'page' => $page,
                    'showing' => $servers->count(),
                    'total' => $allServers->count()
                ],
                'data' => $servers->toArray()
            ]);

            return $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

        } catch (InvalidArgumentException|Exception $e) {
            $this->logger->error("Failed to fetch server list: {$e->getMessage()}", ['exception' => $e]);
            return $this->json("Unable to fetch server list", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }
}
