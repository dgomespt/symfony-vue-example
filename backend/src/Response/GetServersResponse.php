<?php

namespace App\Response;

use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;

final class GetServersResponse implements JsonSerializable
{
    private int $status = Response::HTTP_OK;

    public function __construct(
        protected Meta $meta,
        protected array $data
    ){
    }

    public function jsonSerialize(): array
    {
        return [
            'meta' => $this->meta->toArray(),
            'data' => $this->data
        ];
    }
}