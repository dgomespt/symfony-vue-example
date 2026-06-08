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
    public int $page;

    #[Positive]
    #[Type('numeric')]
    public int $itemsPerPage;

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
    public array $filters = [];

    #[Collection(
        fields: [
            'ram' => new Choice(choices: ['asc', 'desc']),
            'price' => new Choice(choices: ['asc', 'desc']),
            'model' => new Choice(choices: ['asc', 'desc']),
            'hdd' => new Choice(choices: ['asc', 'desc']),
            'location' => new Choice(choices: ['asc', 'desc']),
        ],
        allowMissingFields: true,
        extraFieldsMessage: 'Allowed keys are (ram, price, model, hdd, location) and values are (asc, desc)'
    )]
    public array $order = [];

    public function __construct(
        int $page,
        int $itemsPerPage,
        array $order,
        array $filters,
    )
    {
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
        $this->filters = $filters;
        $this->order = $order;
    }


    public static function fromRequest(Request $request): GetServersRequest
    {
        return new GetServersRequest(
            intval($request->query->get('page', 1)),
            intval($request->query->get('itemsPerPage', 50)),
            $request->query->all('order'),
            $request->query->all('filters')
        );
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