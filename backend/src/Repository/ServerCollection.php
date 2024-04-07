<?php

namespace App\Repository;

use App\Entity\Server;
use App\Error\InvalidFilterFieldError;
use App\Error\InvalidOrderDirectionError;
use App\Error\InvalidOrderFieldError;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use InvalidArgumentException;

class ServerCollection extends ArrayCollection
{
    public function add(mixed $element): void
    {
        if (!$element instanceof Server) {
            throw new InvalidArgumentException('Element must be instance of Server');
        }

        parent::add($element);
    }

    /**
     * @throws InvalidOrderFieldError
     * @throws Exception
     */
    public function order(string $name, $direction = 'asc'): static
    {
        match($direction){
            'asc', 'desc' => null,
            default => throw new InvalidOrderDirectionError("Invalid order direction: $direction")
        };

        $getter = 'get'.ucfirst($name);
        if(!method_exists(Server::class, $getter)){
            throw new InvalidOrderFieldError("Trying to order by unknown property: $name");
        }

        $i = $this->getIterator();
        $i->uasort(function ($a, $b) use ($getter,$name, $direction) {

            $val1 = $this->parseSortableValue($a->$getter(), $name);
            $val2 = $this->parseSortableValue($b->$getter(), $name);

            if ($val1 == $val2) {
                return 0;
            }
            return ($val1 < $val2) ? -1 : 1;
        });

        $results = iterator_to_array($i);
        return new static($direction === 'asc' ? $results : array_reverse($results));
    }

    /**
     * @param array $filters
     * @return $this
     * @throws InvalidFilterFieldError|Exception
     */
    public function applyFilters(array $filters): static{

        $allServers = $this->createFrom($this->getIterator()->getArrayCopy());

        foreach($filters as $name => $value){
            $getter = 'get'.ucfirst($name);
            if(method_exists(Server::class, $getter)){
                $allServers = $allServers->filter(function($server) use ($getter, $name, $value) {
                    return $server->$getter() == $value;
                });
            }else{
                throw new InvalidFilterFieldError("Trying to filter by unknown property: $name");
            }
        }
        return $allServers;
    }

    protected function parseSortableValue(string $value, string $name): string|int|float
    {
        return match($name) {
            'ram' => intval(trim($value)),
            'price' => floatval(preg_replace('/[^0-9.]/', '', $value)),
            default => trim($value)
        };
    }


}