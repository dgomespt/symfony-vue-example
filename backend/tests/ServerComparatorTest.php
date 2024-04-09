<?php

namespace App\Tests;

use App\Comparator\ServerComparator;
use App\Entity\Hdd;
use App\Entity\Price;
use App\Entity\Server;
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

        $a = new Server('1', 'a', $ram1, new Hdd(1,1), 'a', new Price(1, 'USD'));
        $b = new Server('2', 'b', $ram2, new Hdd(1,1), 'a', new Price(1, 'USD'));

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

        $a = new Server('', '', '', Hdd::fromString($hdd1), '', new Price(1, 'USD'));
        $b = new Server('', '', '', Hdd::fromString($hdd2), '', new Price(1, 'USD'));

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
     * @param Price $price1
     * @param Price $price2
     * @param int $expected
     * @return void
     */
    public function testComparePrice(Price $price1, Price $price2, int $expected): void
    {

        $a = new Server('', '', '', new Hdd(1,1), '', $price1);
        $b = new Server('', '', '', new Hdd(1,1), '', $price2);

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
