<?php

namespace App\Response;

use App\Servers\ServerCollection;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;

final class GetServersResponse implements JsonSerializable
{
    private int $status = Response::HTTP_OK;

    public function __construct(
        protected Meta $meta,
        protected ServerCollection $data
    ){
    }

    public function jsonSerialize(): array
    {
        return [
            'meta' => $this->meta->toArray(),
            'data' => $this->data->map(fn($server) => $server->toArray())->getValues()
        ];
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function getData(): ServerCollection
    {
        return $this->data;
    }


}