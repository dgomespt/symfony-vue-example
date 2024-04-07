<?php

namespace App\UseCase;

use App\Interface\RepositoryInterface;
use App\Repository\ServerCollection;
use App\Response\GetServersResponse;
use App\Response\Meta;
use Exception;

readonly class GetServersUseCase
{

    public function __construct(
        protected RepositoryInterface $serverRepository
    )
    {
    }

    /**
     * @param int $page
     * @param int $itemsPerPage
     * @param array $filters
     * @param array $order
     * @return GetServersResponse
     * @throws Exception
     */
    public function handle(int $page, int $itemsPerPage, array $filters, ?array $order): GetServersResponse
    {
        $start = ($page - 1) * $itemsPerPage;

        /** @var ServerCollection $allServers */
        $allServers = $this->serverRepository->all();

        $allServers = $allServers->applyFilters($filters);

        if(null !== $order){
            $order = array_slice($order, 0, 1);
            $allServers = $allServers->order(key($order), $order[key($order)]);
        }

        $servers = new ServerCollection($allServers->slice($start, $itemsPerPage));

        return new GetServersResponse(
            new Meta($page,
                $servers->count(),
                $allServers->count()
            ), $servers->toArray());


    }
}