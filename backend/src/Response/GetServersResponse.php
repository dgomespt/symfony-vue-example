<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

final class GetServersResponse
{

    public function __construct(
        protected Meta $meta,
        protected array $data
    ){
    }

    public function toArray(): array
    {
        return [
            'meta' => $this->meta->toArray(),
            'data' => $this->data
        ];
    }
}