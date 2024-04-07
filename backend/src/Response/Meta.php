<?php

namespace App\Response;

class Meta
{

    public function __construct(
        protected int $page, protected int $showing, protected int $total)
    {
    }

    public function toArray(): array{
        return [
            'page' => $this->page,
            'showing' => $this->showing,
            'total' => $this->total
        ];
    }
}