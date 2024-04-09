<?php

namespace App\Tests;

use App\Controller\ServersController;
use App\Entity\Server;
use App\Interface\RepositoryInterface;
use App\Repository\ServerCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GetServerUseCaseTest extends KernelTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

    }

    public function testBasicResponse(): void
    {
        $server = [
            'id' => 1,
            'model' => 'test',
            'ram' => '16GBDDR4',
            'hdd' => '500GB',
            'location' => 'AMS',
            'price' => '€50.00'
        ];

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->expects(self::once())->method('all')->willReturn(new ServerCollection([
            new Server(...$server),
        ]));
        static::getContainer()->set(RepositoryInterface::class, $repository);

        $controller = static::getContainer()->get(ServersController::class);
        $response = $controller->index(new Request([]));
        $this->assertInstanceOf(JsonResponse::class, $response);

        $result = json_decode($response->getContent(), true, 512, JSON_OBJECT_AS_ARRAY);
        $this->assertEquals([
            'meta' => [
                'page' => 1,
                'showing' => 1,
                'total' => 1
            ],
            'data' => [
                $server
            ]
        ], $result);

    }

    public function testEmptyResultsReturned(){

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->expects(self::once())->method('all')->willReturn(new ServerCollection([]));
        static::getContainer()->set(RepositoryInterface::class, $repository);

        $controller = static::getContainer()->get(ServersController::class);
        $response = $controller->index(new Request([]));

        $result = json_decode($response->getContent(), true, 512, JSON_OBJECT_AS_ARRAY);
        $this->assertEquals([
            'meta' => [
                'page' => 1,
                'showing' => 0,
                'total' => 0
            ],
            'data' => []
        ], $result);
    }

}
