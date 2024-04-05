<?php

namespace App\Repository;

use App\Entity\Server;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use Traversable;

class ServerCollection implements IteratorAggregate, Countable
{

    public function __construct(
        protected array $servers = []
    )
    {

    }

    protected function createFromArray(array $servers): ServerCollection
    {
        return new static($servers);
    }

    public function add(Server $server): void
    {
        $this->servers[] = $server;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->servers);
    }

    public function count(): int
    {
        return count($this->servers);
    }

    public function filter(Closure $closure): ServerCollection
    {
        return $this->createFromArray(array_filter($this->servers, $closure));
    }

    public function take(int $count, int $offset = 0): ServerCollection
    {
        return $this->createFromArray(array_slice($this->servers, $offset, $count));
    }

    public function toArray(): array
    {
        return array_map(fn(Server $server) => $server->jsonSerialize(), $this->servers);
    }

}