<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

final class GetServersRequest
{

    #[Positive]
    #[Type('numeric')]
    public mixed $page;

    #[Positive]
    #[Type('numeric')]
    public mixed $itemsPerPage;

    #[Collection(
        fields: [
            'hddType' => new Type('string'),
            'location' => new Type('string'),
            'ram' => new Type('string'),
            'storage' => new Type('string'),
        ],
        allowMissingFields: true,
        extraFieldsMessage: 'Allowed keys are (hddType, location, ram, storage)'
    )]
    public mixed $filters = [];

    #[Collection(
        fields: [
            'ram' => new Choice(['asc', 'desc']),
            'price' => new Choice(['asc', 'desc']),
            'model' => new Choice(['asc', 'desc']),
            'hdd' => new Choice(['asc', 'desc']),
            'location' => new Choice(['asc', 'desc']),
        ],
        allowMissingFields: true,
        extraFieldsMessage: 'Allowed keys are (ram, price, model, hdd, location) and values are (asc, desc)'
    )]
    public mixed $order = [];

    public static function fromRequest(Request $request): GetServersRequest
    {
        $r = new GetServersRequest();
        $r->page = $request->get('page', 1);
        $r->itemsPerPage = $request->get('itemsPerPage', 50);
        $r->filters = $request->get('filters', []);
        $r->order = $request->get('order', []);
        return $r;
    }

    public function toArray(): array
    {
        return [
            'page' => intval($this->page),
            'itemsPerPage' => intval($this->itemsPerPage),
            'filters' => $this->filters,
            'order' => $this->order
        ];
    }

}