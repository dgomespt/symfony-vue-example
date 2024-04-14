<?php

namespace App\Servers\Entity;

use NumberFormatter;
use Symfony\Component\Intl\Currencies;

final readonly class Price
{
    public const string DEFAULT_CURRENCY_CODE = 'EUR';

    public function __construct(
        private int    $price,
        private string $currencyCode
    )
    {

    }

    public static function fromString(string $price): Price
    {
        $withoutWhiteSpace = preg_replace('/\s+/', '', $price);
        preg_match('/(\p{Sc})?(\d+\.\d{2})/u', $withoutWhiteSpace, $matches);

        if($matches){
            $formatter = new NumberFormatter('en', NumberFormatter::CURRENCY);
            $parsed = $formatter->parseCurrency($matches[0], $currencyCode);

            if(!$parsed){
                $parsed = $matches[0];
            }

        }else{
            $parsed = $withoutWhiteSpace;
        }
        return new self(ceil(round($parsed*100,2)), $currencyCode ?? self::DEFAULT_CURRENCY_CODE);

    }

    public function toString(): string
    {
        $symbol = Currencies::getSymbol($this->currencyCode);

        return $symbol.number_format($this->price/100, 2);
    }


    public function getValue(): int
    {
        return $this->price;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
}