<?php

namespace App\Tests;

use App\Servers\Entity\Hdd;
use App\Servers\Entity\Price;
use App\Servers\Entity\Ram;
use App\Servers\Entity\Server;
use App\Servers\ServerComparator;
use PHPUnit\Framework\TestCase;

class ServerComparatorTest extends TestCase
{
    /**
     * @dataProvider provideRamTests
     * @param string $ram1
     * @param string $ram2
     * @param int $expected
     * @return void
     */
    public function testCompareRam(string $ram1, string $ram2, int $expected): void
    {

        $a = new Server('1', 'a', Ram::fromString($ram1), Hdd::fromString('1x1TBSATA'), 'a', new Price(1, 'USD'));
        $b = new Server('2', 'b', Ram::fromString($ram2), Hdd::fromString('1x1TBSATA'), 'a', new Price(1, 'USD'));

        $c = new ServerComparator();

        $this->assertEquals($expected, $c->compareRam($a, $b));
    }

    public function provideRamTests(): array
    {
        return [
            [
                '128GBDDR5',
                '128GBDDR5',
                0
            ],
            [
                '64GBDDR5',
                '128GBDDR5',
                -1
            ],
            [
                '256GBDDR5',
                '128GBDDR5',
                1
            ],
        ];
    }

    /**
     * @dataProvider provideHddTests
     * @param string $hdd1
     * @param string $hdd2
     * @param int $expected
     * @return void
     */
    public function testCompareHdd(string $hdd1, string $hdd2, int $expected): void{

        $a = new Server('', '', Ram::fromString('8GBDDR5'), Hdd::fromString($hdd1), '', new Price(1, 'USD'));
        $b = new Server('', '', Ram::fromString('8GBDDR5'), Hdd::fromString($hdd2), '', new Price(1, 'USD'));

        $c = new ServerComparator();
        $this->assertEquals($expected, $c->compareHdd($a, $b));
    }

    public function provideHddTests(): array
    {
        return [
            [
                '1x500GB',
                '1x500GB',
                0
            ],
            [
                '1x500GB',
                '2x500GB',
                -1
            ],
            [
                '2x500GB',
                '1x500GB',
                1
            ],
            [
                '1x2TB',
                '4x500GB',
                0
            ],
        ];
    }

    /**
     * @dataProvider providePriceTests
     * @param \App\Servers\Entity\Price $price1
     * @param Price $price2
     * @param int $expected
     * @return void
     */
    public function testComparePrice(Price $price1, Price $price2, int $expected): void
    {

        $a = new Server('', '', Ram::fromString('8GBDDR5'), Hdd::fromString('1x1TBSATA'), '', $price1);
        $b = new Server('', '', Ram::fromString('8GBDDR5'), Hdd::fromString('1x1TBSATA'), '', $price2);

        $c = new ServerComparator();
        $this->assertEquals($expected, $c->comparePrice($a, $b));

    }

    public function providePriceTests(): array{

        return [
            [
                new Price(1, 'USD'),
                new Price(1, 'USD'),
                0
            ],
            [
                new Price(1, 'USD'),
                new Price(2, 'USD'),
                -1
            ],
            [
                new Price(2, 'USD'),
                new Price(1, 'USD'),
                1
            ],
            [
                new Price(1, 'EUR'),
                new Price(1, 'USD'),
                0
            ],
        ];
    }
}
