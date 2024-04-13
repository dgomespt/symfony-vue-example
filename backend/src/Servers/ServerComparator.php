<?php

namespace App\Servers;

use App\Servers\Entity\Server;

readonly class ServerComparator
{

    public function compare(Server $a, Server $b): array
    {
        return [
            'ram' => $this->compareRam($a, $b),
            'hdd' => $this->compareHdd($a, $b),
            'price' => $this->comparePrice($a, $b),
            'model' => $a->getModel() <=> $b->getModel(),
            'location' => $a->getLocation() <=> $b->getLocation()
        ];
    }

    public function compareRam(Server $a, Server $b): int
    {
        return intval($a->getRam()->getSize()) <=> intval($b->getRam()->getSize());
    }

    public function compareHdd(Server $a, Server $b): int{
        return $a->getHdd()->getTotalCapacityInGb() <=> $b->getHdd()->getTotalCapacityInGb();
    }

    public function comparePrice(Server $a, Server $b): int{
        return $a->getPrice()->getValue() <=> $b->getPrice()->getValue();
    }
}