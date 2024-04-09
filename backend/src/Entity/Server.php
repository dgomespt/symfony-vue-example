<?php

namespace App\Entity;

use JsonSerializable;

readonly class Server implements JsonSerializable
{
    public function __construct(
        private string $id,
        private string $model,
        private string $ram,
        private Hdd $hdd,
        private string $location,
        private Price $price){
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

    public function getHdd(): Hdd
    {
        return $this->hdd;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }


    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    protected function getHddValue(): string{

        preg_match('/(\d+)x(\d+)(\D{2})/', $this->hdd, $matches);

        if($matches){
            $multiplier = $matches[3] === 'TB' ? 1000 : 1;
            return intval($matches[1]) * intval($matches[2]) * $multiplier;
        }

        return $this->hdd;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'model' => $this->model,
            'ram' => $this->ram,
            'hdd' => $this->hdd->toString(),
            'location' => $this->location,
            'price' => $this->price->toString()
        ];
    }
}