<?php

namespace App\Entity;

readonly class Ram
{
    public const DEFAULT_UNIT = 'GB';

    private string $unit;

    public function __construct(
        private int   $size,
        private string $type,
        ?string $unit = self::DEFAULT_UNIT
    )
    {
        $this->unit = strtoupper($unit);
    }

    public static function fromString(string $value): static
    {
        preg_match('/(\d+)(\w{2})(\w+)/', $value, $matches);

        if($matches){
            $size = intval($matches[1]);
            $type = $matches[3];
            $unit = $matches[2];
        }

        return new static($size ?? 0, $type ?? '', $unit ?? self::DEFAULT_UNIT);
    }

    public function toString(): string
    {
        return "{$this->size}{$this->unit}{$this->type}";
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getType(): string
    {
        return $this->type;
    }

}