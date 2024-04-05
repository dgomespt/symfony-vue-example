<?php

namespace App\Entity;

use JsonSerializable;

readonly class Server implements JsonSerializable
{
    public function __construct(
        private string $id,
        private string $model,
        private string $ram,
        private string $hdd,
        private string $location,
        private string $price){
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'model' => $this->model,
            'ram' => $this->ram,
            'hdd' => $this->hdd,
            'location' => $this->location,
            'price' => $this->price
        ];
    }
}