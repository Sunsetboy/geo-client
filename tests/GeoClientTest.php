<?php


use GeoServiceClient\exceptions\NotFoundException;
use GeoServiceClient\exceptions\UnauthorizedException;
use GeoServiceClient\GeoClient;
use GeoServiceClient\models\Region;
use GeoServiceClient\models\Town;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GeoClientTest extends TestCase
{
    /**
     * @dataProvider providerGetTownById
     * @param string $jsonResponse
     * @param array $params
     * @param integer $httpStatusCode
     * @param string|null $expectedException
     */
    public function testGetTownById($jsonResponse, $params, $httpStatusCode, $expectedException)
    {
        if (!is_null($expectedException)) {
            $this->expectException($expectedException);
        }

        $geoClient = $this->getGeoClientWithMockedHttpClient($jsonResponse, $httpStatusCode);

        $town = $geoClient->getTownById(33, $params);

        $this->assertInstanceOf(Town::class, $town);

        if (isset($params['with'])) {
            $this->assertEquals("Липецкая область", $town->getRegion()->getName());
        } else {
            $this->assertEquals("Апрелевка", $town->getName());
        }
    }

    public function providerGetTownById(): array
    {
        return [
            'not empty response' => [
                'jsonResponse' => json_encode([
                    [
                        "id" => 33,
                        "name" => "Апрелевка",
                        "alias" => "aprelevka",
                        "size" => 0,
                        "lat" => "55.545166",
                        "lng" => "37.073220",
                        "isCapital" => 0
                    ],
                ]),
                'params' => [],
                'httpStatusCode' => 200,
                'expectedException' => null,
            ],
            'town with region' => [
                'jsonResponse' => json_encode([
                    [
                        "id" => 33,
                        "name" => "Лименда",
                        "alias" => "limenda",
                        "size" => 0,
                        "lat" => "55.545166",
                        "lng" => "37.073220",
                        "isCapital" => 0,
                        "region" => [
                            "id" => 1,
                            "name" => 'Липецкая область',
                            "alias" => 'lipetskaya-oblast',
                        ],
                    ]
                ]),
                'params' => [
                    'with' => 'region',
                ],
                'httpStatusCode' => 200,
                'expectedException' => null,
            ],
            'not authorized' => [
                'jsonResponse' => json_encode([
                    "id" => 55,
                ]),
                'params' => [],
                'httpStatusCode' => 401,
                'expectedException' => UnauthorizedException::class,
            ],
            'town not found' => [
                'jsonResponse' => json_encode([
                    "Town not found"
                ]),
                'params' => [],
                'httpStatusCode' => 404,
                'expectedException' => NotFoundException::class,
            ],
        ];
    }

    public function testGetRegionById()
    {
        $jsonResponse = json_encode([
            [
                "id" => 33,
                "name" => "Липецкая область",
                "countryId" => 2,
                "alias" => "lipetskaya",
            ]
        ]);
        $httpStatusCode = 200;

        $geoClient = $this->getGeoClientWithMockedHttpClient($jsonResponse, $httpStatusCode);

        $region = $geoClient->getRegionById(33);
        $this->assertInstanceOf(Region::class, $region);

        $this->assertEquals("Липецкая область", $region->getName());
    }

    /**
     * Создает экземпляр класса GeoClient с мокнутым HttpClient
     * @param string $jsonResponse
     * @param int $httpStatusCode
     * @return GeoClient
     */
    private function getGeoClientWithMockedHttpClient(string $jsonResponse, int $httpStatusCode): GeoClient
    {
        $httpClientMock = $this->createMock(Client::class);
        $httpClientResponseMock = $this->createMock(Response::class);
        $httpClientResponseMock->method('getBody')->willReturn($jsonResponse);
        $httpClientResponseMock->method('getStatusCode')->willReturn($httpStatusCode);
        $httpClientMock->method('request')->willReturn($httpClientResponseMock);

        $geoClient = new GeoClient('http://test.local', 'my_token');
        $geoClient->setHttpClient($httpClientMock);

        return $geoClient;
    }

    public function testGetTownsByIds()
    {
        $jsonResponse = json_encode([
            [
                "id" => 1,
                "name" => "Бобруйск",
                "alias" => "bobruysk",
            ],
            [
                "id" => 2,
                "name" => "Когалым",
                "alias" => "kogalym",
            ],
        ]);
        $httpStatusCode = 200;

        $geoClient = $this->getGeoClientWithMockedHttpClient($jsonResponse, $httpStatusCode);

        $towns = $geoClient->getTownsByIds([1, 2]);

        $this->assertIsArray($towns);
        $this->assertInstanceOf(Town::class, $towns[0]);

        $this->assertEquals("Бобруйск", $towns[0]->getName());
    }

    public function testGetTowns()
    {
        $jsonResponse = json_encode([
            [
                "id" => 1,
                "name" => "Бобруйск",
                "alias" => "bobruysk",
                "region" => [
                    "id" => 1,
                    "name" => 'Липецкая область',
                    "alias" => 'lipetskaya-oblast',
                ],
            ],
            [
                "id" => 2,
                "name" => "Бобровнич",
                "alias" => "kogalym",
                "region" => [
                    "id" => 1,
                    "name" => 'Липецкая область',
                    "alias" => 'lipetskaya-oblast',
                ],
            ],
        ]);
        $httpStatusCode = 200;

        $geoClient = $this->getGeoClientWithMockedHttpClient($jsonResponse, $httpStatusCode);

        $towns = $geoClient->getTowns(10, 1, 1, 'Бобр');

        $this->assertIsArray($towns);
        $this->assertInstanceOf(Town::class, $towns[0]);

        $this->assertEquals("Бобруйск", $towns[0]->getName());
        $this->assertEquals('Бобровнич', $towns[1]->getName());
        $this->assertEquals('Липецкая область', $towns[1]->getRegion()->getName());
    }

    public function testGetRegions()
    {
        $jsonResponse = json_encode([
            [
                "id" => 1,
                "name" => "Брестская область",
                "alias" => "brestskaya-oblast",
            ],
            [
                "id" => 2,
                "name" => "Витебская область",
                "alias" => "vitebskaya-oblast",
            ],
        ]);
        $httpStatusCode = 200;

        $geoClient = $this->getGeoClientWithMockedHttpClient($jsonResponse, $httpStatusCode);

        $regions = $geoClient->getRegions(10, 1);

        $this->assertIsArray($regions);
        $this->assertInstanceOf(Region::class, $regions[0]);

        $this->assertEquals("Брестская область", $regions[0]->getName());
    }

    /**
     * @dataProvider providerUpdateTown
     * @param string $jsonResponse
     * @param integer $httpStatusCode
     * @param string $expectedException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUpdateTown($jsonResponse, $httpStatusCode, $expectedException)
    {
        if (!is_null($expectedException)) {
            $this->expectException($expectedException);
        }

        $geoClient = $this->getGeoClientWithMockedHttpClient($jsonResponse, $httpStatusCode);

        $town = $geoClient->updateTown(1, ['alias' => 'rezinovaya']);

        $this->assertInstanceOf(Town::class, $town);
        $this->assertEquals("rezinovaya", $town->getAlias());
    }

    public function providerUpdateTown(): array
    {
        return [
            'not empty response' => [
                'jsonResponse' => json_encode([
                    "id" => 33,
                    "name" => "Апрелевка",
                    "regionId" => 25,
                    "alias" => "rezinovaya",
                    "size" => 0,
                    "lat" => "55.545166",
                    "lng" => "37.073220",
                    "isCapital" => 0
                ]),
                'httpStatusCode' => 200,
                'expectedException' => null,
            ],
            'not authorized' => [
                'jsonResponse' => json_encode([
                    "id" => 55,
                ]),
                'httpStatusCode' => 401,
                'expectedException' => UnauthorizedException::class,
            ],
            'town not found' => [
                'jsonResponse' => json_encode([
                    "Town not found"
                ]),
                'httpStatusCode' => 404,
                'expectedException' => NotFoundException::class,
            ],
        ];
    }
}
