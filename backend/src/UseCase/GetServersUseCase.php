<?php

namespace App\UseCase;

use App\Interface\RepositoryInterface;
use App\Repository\ServerCollection;
use App\Response\GetServersResponse;
use App\Response\Meta;
use Exception;

final readonly class GetServersUseCase
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
     * @param array|null $order
     * @return GetServersResponse
     * @throws Exception
     */
    public function handle(int $page, int $itemsPerPage, array $filters, ?array $order): GetServersResponse
    {
        $start = ($page - 1) * $itemsPerPage;

        /** @var ServerCollection $allServers */
        $allServers = $this->serverRepository->all();

        $allServers = $allServers->applyFilters($filters);

        if (!empty($order)) {
            $order = array_slice($order, 0, 1);
            $allServers = $allServers->order(key($order), $order[key($order)]);
        }

        $servers = new ServerCollection($allServers->slice($start, $itemsPerPage));

        return new GetServersResponse(
            new Meta($page,
                $itemsPerPage,
                $servers->count(),
                $allServers->count()
            ), array_values($servers->toArray()));
    }
}