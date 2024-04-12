<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

final class GetServersRequest extends BaseRequest
{

    #[Positive]
    #[Type('numeric')]
    protected mixed $page = 1;

    #[Positive]
    #[Type('numeric')]
    protected mixed $itemsPerPage = 50;

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
    protected mixed $filters = [];

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
    protected mixed $order = [];


    /**
     * Override because we want the query string data to be populated, not the request body
     * @return void
     */
    protected function populate(): void
    {
        $queryData = [];
        $qs = $this->getRequest()->getQueryString();
        parse_str($qs, $queryData);

        foreach ($queryData as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = ($value);
            }
        }
    }

    public function getPage(): ?int
    {
        return intval($this->page);
    }

    public function getItemsPerPage(): ?int
    {
        return intval($this->itemsPerPage);
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getOrder(): array
    {
        return $this->order;
    }

}