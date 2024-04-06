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

    public function getId(): string
    {
        return $this->id;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getRam(): string
    {
        return $this->ram;
    }

    public function getHdd(): string
    {
        return $this->hdd;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getPrice(): string
    {
        return $this->price;
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