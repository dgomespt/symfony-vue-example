<?php

namespace App\Repository;

use App\Entity\Server;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class ServerCollection extends ArrayCollection
{
    public function add(mixed $element): void
    {
        if (!$element instanceof Server) {
            throw new \InvalidArgumentException('Element must be instance of Server');
        }

        parent::add($element);
    }

    /**
     * @throws Exception
     */
    public function sort(string $name, $direction = 'asc'): static
    {
        $i = $this->getIterator();
        $i->uasort(function ($a, $b) use ($name, $direction) {

            $val1 = $a->{'get'.ucfirst($name)}();
            $val2 = $b->{'get'.ucfirst($name)}();

            $val1 = $this->parseSortableValue($val1, $name);
            $val2 = $this->parseSortableValue($val2, $name);

            if ($val1 == $val2) {
                return 0;
            }
            return ($val1 < $val2) ? -1 : 1;
        });

        $results = iterator_to_array($i);
        return new static($direction === 'asc' ? $results : array_reverse($results));
    }

    public function parseSortableValue(string $value, string $name): mixed
    {
        return match($name) {
            'ram' => intval(trim($value)),
            'price' => floatval(preg_replace('/[^0-9.]/', '', $value)),
            default => trim($value)
        };
    }


}