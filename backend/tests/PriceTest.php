<?php

namespace App\Tests;

use App\Entity\Price;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    /**
     * @dataProvider providePriceStrings
     * @param string $input
     * @param int $expectedValue
     * @param string $expectedCurrency
     * @return void
     */
    public function testStringToPrice(string $input, int $expectedValue, string $expectedCurrency): void
    {
        $priceObj = Price::fromString($input);

        $this->assertEquals($expectedValue, $priceObj->getValue());
        $this->assertEquals($expectedCurrency, $priceObj->getCurrencyCode());
    }

    /**
     * @dataProvider providePriceObjects
     * @param Price $price
     * @param string $expected
     * @return void
     */
    public function testPriceToString(Price $price, string $expected): void{

        $this->assertEquals($expected, $price->toString());
    }

    public function providePriceStrings(): array
    {
        return [
            'sanitized value with €' => ['€10.99', 1099, 'EUR'],
            'sanitized value with $' => ['$99.90', 9990, 'USD'],
            'sanitized value with £' => ['£9.99', 999, 'GBP'],
            'no symbol defaults to EUR' => ['9.99', 999, 'EUR'],
            'bunch of characters padding the value' => ['lasdfhaisdlf$49.90fds',4990,'USD'],
            'whitespace between symbol and value' => ['£ 9.99', 999, 'GBP'],
        ];
    }

    public function providePriceObjects(): array{

        return [
            '10.99' => [new Price(1099, 'EUR'), '€10.99'],
            '99.90' => [new Price(9990, 'USD'), '$99.90'],
            '9.99' => [new Price(999, 'GBP'), '£9.99']
        ];
    }
}
