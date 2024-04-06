<?php

namespace App\Repository;

use App\Entity\Server;
use App\Interface\RepositoryInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class ServerRepository implements RepositoryInterface
{

    public function __construct(
        protected CacheInterface $serverCache
    ){
    }

    protected function loadFromFile(): ServerCollection
    {
        $xls = IOFactory::load('/application/data/LeaseWeb_servers_filters_assignment.xlsx');

        $xls->setActiveSheetIndex(0);
        $rows = $xls->getActiveSheet()->toArray();

        $servers = new ServerCollection();
        array_shift($rows);

        foreach($rows as $idx => $row) {
            $servers->add(new Server(++$idx,...$row));
        }

        return $servers;

    }

    /**
     * @throws InvalidArgumentException
     */
    public function all(): ServerCollection
    {
        return $this->serverCache->get('servers', function(ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->set($this->loadFromFile());
            return $item->get();
        });
    }
}