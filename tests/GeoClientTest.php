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
     * @param integer $httpStatusCode
     * @param string|null $expectedException
     */
    public function testGetTownById($jsonResponse, $httpStatusCode, $expectedException)
    {
        if (!is_null($expectedException)) {
            $this->expectException($expectedException);
        }

        $geoClient = $this->getGeoClientWithMockedHttpClient($jsonResponse, $httpStatusCode);

        $town = $geoClient->getTownById(33);

        $this->assertInstanceOf(Town::class, $town);
        $this->assertEquals("Апрелевка", $town->getName());
    }

    public function providerGetTownById(): array
    {
        return [
            'not empty response' => [
                'jsonResponse' => json_encode([
                    "id" => 33,
                    "name" => "Апрелевка",
                    "regionId" => 25,
                    "alias" => "aprelevka",
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

    public function testGetRegionById()
    {
        $jsonResponse = json_encode([
            "id" => 33,
            "name" => "Липецкая область",
            "countryId" => 2,
            "alias" => "lipetskaya",
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
}