<?php

namespace App\Repository;

use App\Entity\Hdd;
use App\Entity\Price;
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

        $headers = [];
        $servers = new ServerCollection();

        foreach($rows as $idx => $row) {

            if($idx === 0){
                $headers = array_map(fn($row)=> strtolower($row), array_splice($row, 0, 5));
                continue;
            }

            $row = array_combine(array_values($headers), array_splice($row, 0, count($headers)));

            $serverObj = new Server(
                $idx,
                $row['model'],
                $row['ram'],
                Hdd::fromString($row['hdd']),
                $row['location'],
                Price::fromString($row['price'])
            );

            $servers->add($serverObj);
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