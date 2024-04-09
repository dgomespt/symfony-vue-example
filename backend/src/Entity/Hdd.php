<?php

namespace App\Entity;

class Hdd
{
    private string $unit = 'GB';
    public function __construct(
        private readonly int $capacity,
        private readonly int $numberOfDisks
    ){

    }

    public static function fromString(string $value): Hdd{

        $capacity = $value;
        $numberOfDisks = 1;

        if (preg_match('/(\d+)x(\d+)(\D{2})/', $value, $matches)) {
            $capacity = intval($matches[2]);
            $numberOfDisks = intval($matches[1]);
        }

        $hdd = new static($capacity, $numberOfDisks);
        $hdd->unit = $matches[3] ?? 'GB';

        return $hdd;

    }

    public function getTotalCapacityInGb(): int
    {
        $multiplier = match ($this->unit) {
            'PB' => 1000^2,
            'TB' => 1000,
            default => 1
        };

        return $this->numberOfDisks * $this->capacity * $multiplier;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getNumberOfDisks(): int
    {
        return $this->numberOfDisks;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function toString(): string{

        return "{$this->numberOfDisks}x{$this->capacity}{$this->unit}";
    }
}