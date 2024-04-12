<?php

namespace App\Tests;

use App\Controller\ServersController;
use App\Entity\Hdd;
use App\Entity\Price;
use App\Entity\Ram;
use App\Entity\Server;
use App\Interface\RepositoryInterface;
use App\Repository\ServerCollection;
use App\Request\GetServersRequest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetServerUseCaseTest extends KernelTestCase
{

    private RepositoryInterface $repositoryMock;
    private ValidatorInterface $validatorMock;
    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repositoryMock = $this->createMock(RepositoryInterface::class);

        $this->validatorMock = $this->createMock(ValidatorInterface::class);

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

        $response = $controller->index(new GetServersRequest( $this->validatorMock));
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
        $response = $controller->index(new GetServersRequest( $this->validatorMock));

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

}
