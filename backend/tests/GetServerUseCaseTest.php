<?php

namespace App\Tests;

use App\Controller\ServersController;
use App\Interface\RepositoryInterface;
use App\Request\GetServersRequest;
use App\Response\ErrorResponse;
use App\Response\GetServersResponse;
use App\Servers\Entity\Hdd;
use App\Servers\Entity\Price;
use App\Servers\Entity\Ram;
use App\Servers\Entity\Server;
use App\Servers\ServerCollection;
use App\UseCase\GetServersUseCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GetServerUseCaseTest extends KernelTestCase
{

    private RepositoryInterface $repositoryMock;
    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repositoryMock = $this->createMock(RepositoryInterface::class);

    }

    public function testBasicResponse(): void
    {
        $server = [
            'id' => 1,
            'model' => 'test',
            'ram' => '16GBDDR4',
            'hdd' => '1x500GBSSD',
            'location' => 'AMS',
            'price' => '€50.00'
        ];

        $this->repositoryMock->expects(self::once())->method('all')->willReturn(new ServerCollection([
            new Server($server['id'], $server['model'], Ram::fromString($server['ram']), Hdd::fromString($server['hdd']), $server['location'], Price::fromString($server['price'])),
        ]));
        static::getContainer()->set(RepositoryInterface::class, $this->repositoryMock);

        $controller = static::getContainer()->get(ServersController::class);

        $response = $controller->index(new Request([]));
        $this->assertInstanceOf(JsonResponse::class, $response);

        $result = json_decode($response->getContent(), true, 512, JSON_OBJECT_AS_ARRAY);
        $this->assertEquals([
            'meta' => [
                'page' => 1,
                'showing' => 1,
                'total' => 1,
                'start' => 1,
                'end' => 1,
                'itemsPerPage' => 50
            ],
            'data' => [
                $server
            ]
        ], $result);

    }

    public function testEmptyResultsReturned(){

        $this->repositoryMock->expects(self::once())->method('all')->willReturn(new ServerCollection([]));
        static::getContainer()->set(RepositoryInterface::class, $this->repositoryMock);

        $controller = static::getContainer()->get(ServersController::class);
        $response = $controller->index(new Request([]));

        $result = json_decode($response->getContent(), true, 512, JSON_OBJECT_AS_ARRAY);
        $this->assertEquals([
            'meta' => [
                'page' => 1,
                'showing' => 0,
                'total' => 0,
                'start' => 1,
                'end' => 0,
                'itemsPerPage' => 50
            ],
            'data' => []
        ], $result);
    }

    /**
     * @dataProvider provideOrderScenarios
     * @param array $order
     * @param array $expectedIds
     * @return void
     */
    public function testOrderedResults(array $order, array $expectedIds){

        $this->repositoryMock->expects(self::once())->method('all')->willReturn($this->getServerCollection());
        static::getContainer()->set(RepositoryInterface::class, $this->repositoryMock);

        $useCase = static::getContainer()->get(GetServersUseCase::class);

        $response = $useCase->handle(
            new GetServersRequest(
                1,
                50,
                $order,
                []
            )
        );

        $this->assertInstanceOf(GetServersResponse::class, $response);

        $orderedIds = $response->getData()->map(fn($server) => (int)$server->getId())->getValues();

        $this->assertEquals($expectedIds, $orderedIds);
    }

    public function provideOrderScenarios(): array
    {
        return [
            [['model' => 'asc'], [3,1,2]],
            [['model' => 'desc'], [2,1,3]],
            [['price' => 'asc'], [2,3,1]],
            [['price' => 'desc'], [1,3,2]],
            [['ram' => 'asc'], [2,1,3]],
            [['ram' => 'desc'], [3,1,2]],
            [['hdd' => 'asc'], [1,2,3]],
            [['hdd' => 'desc'], [3,2,1]],
            [['location' => 'asc'], [1,2,3]],
            [['location' => 'desc'], [3,2,1]],
        ];
    }

    public function testInvalidOrderKeyReturnsErrorResponse(){

        $this->repositoryMock->expects(self::never())->method('all');
        static::getContainer()->set(RepositoryInterface::class, $this->repositoryMock);

        $useCase = static::getContainer()->get(GetServersUseCase::class);

        $response = $useCase->handle(
            new GetServersRequest(
                1,
                50,
                ['invalidKey' => 'asc'],
                []
            )
        );

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals('validation_failed', $response->getMessage());

    }

    /**
     * @dataProvider provideFiltersScenarios
     * @param array $filters
     * @param array $expectedIds
     * @return void
     */
    public function testFilters(array $filters, array $expectedIds){

        $this->repositoryMock->expects(self::once())->method('all')->willReturn($this->getServerCollection());
        static::getContainer()->set(RepositoryInterface::class, $this->repositoryMock);

        $useCase = static::getContainer()->get(GetServersUseCase::class);

        $response = $useCase->handle(
            new GetServersRequest(
                1,
                50,
                [],
                $filters
            )
        );

        $this->assertInstanceOf(GetServersResponse::class, $response);
        $this->assertCount(count($expectedIds), $response->getData());

        $resultIds = $response->getData()->map(fn($server) => (int)$server->getId())->getValues();
        $this->assertEquals($expectedIds, $resultIds);

    }

    public function provideFiltersScenarios(): array
    {
        return [
            [['hddType' => 'any'], [1,2,3]],
            [['ram' => 'any'], [1,2,3]],
            [['location' => 'any'], [1,2,3]],
            [['storage' => 'any'], [1,2,3]],
            [['hddType' => 'SATA'], [3]],
            [['ram' => '32,8'], [2, 3]],
            [['location' => 'AMS'], [1]],
            [['storage' => '2000'], [2,3]],
            [['ram' => '32,8', 'location' => 'FRA'], [2]],
            [['ram' => '32', 'location' => 'FRA'], []],
        ];
    }


    public function getServerCollection(): ServerCollection
    {
        return new ServerCollection([
            new Server(1, 'AMS', Ram::fromString('16GBDDR4'), Hdd::fromString('1x500GBSSD'), 'AMS', Price::fromString('€50.00')),
            new Server(2, 'AMS1', Ram::fromString('8GBDDR4'), Hdd::fromString('4x500GBSSD'), 'FRA', Price::fromString('€34.90')),
            new Server(3, 'AMD', Ram::fromString('32GBDDR4'), Hdd::fromString('1x2TBSATA'), 'LIS', Price::fromString('€40.00')),
        ]);
    }

}
