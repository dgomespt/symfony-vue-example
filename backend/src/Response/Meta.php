<?php

namespace App\Response;

class Meta
{

    public function __construct(
        protected int $page, protected int $itemsPerPage, protected int $showing, protected int $total)
    {
    }

    public function toArray(): array{
        return [
            'itemsPerPage' => $this->itemsPerPage,
            'page' => $this->page,
            'showing' => $this->showing,
            'total' => $this->total,
            'start' => (($this->page - 1) * $this->itemsPerPage)+1,
            'end' => min($this->page * $this->itemsPerPage, $this->total)
        ];
    }
}