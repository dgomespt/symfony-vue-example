<?php

namespace App\Repository;

use App\Comparator\ServerComparator;
use App\Converter\HddSizeConverter;
use App\Entity\Server;
use App\Error\InvalidFilterFieldError;
use App\Error\InvalidOrderDirectionError;
use App\Error\InvalidOrderFieldError;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use InvalidArgumentException;

class ServerCollection extends ArrayCollection
{

    private ServerComparator $comparator;

    public function __construct(array $elements = []){
        parent::__construct($elements);

        $this->comparator = new ServerComparator();
    }

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

        if(!in_array($direction, ['asc', 'desc'])){
            throw new InvalidOrderDirectionError("Invalid order direction: $direction");
        }

        $getter = 'get'.ucfirst($name);
        if(!method_exists(Server::class, $getter)){
            throw new InvalidOrderFieldError("Trying to order by unknown property: $name");
        }

        $i = $this->getIterator();
        $i->uasort(fn($a, $b) => $this->comparator->compare($a, $b)[$name]);

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

            if($value == 'any') continue;

            $allServers = match (strtolower($name)) {
                'ram' => $allServers->filter($this->filterByRam($value)),
                'storage' => $allServers->filter($this->filterByStorage($value)),
                'location' => $allServers->filter($this->filterByLocation($value)),
                default => throw new InvalidFilterFieldError("Trying to filter by unknown property: $name")
            };
        }
        return $allServers;
    }

    public function filterByRam(string $value): Closure
    {
        $values =  array_unique(explode(',', $value));
        return function(Server $server) use ($values) {
            return in_array($server->getRam(), $values);
        };
    }

    public function filterByStorage( $value): Closure
    {
        return function(Server $server) use ($value) {
            return $server->getHdd()->getTotalCapacityInGb() == $value;
        };
    }

    public function filterByLocation( $value): Closure
    {
        return function(Server $server) use ($value) {
            return strpos($server->getLocation(), $value);
        };
    }

}