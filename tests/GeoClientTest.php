<?php


use GeoServiceClient\exceptions\UnauthorizedException;
use GeoServiceClient\GeoClient;
use GeoServiceClient\models\Region;
use GeoServiceClient\models\Town;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GeoClientTest extends TestCase
{
    public function testGetTownById()
    {
        $jsonResponse = json_encode([
            "id" => 33,
            "name" => "Апрелевка",
            "regionId" => 25,
            "alias" => "aprelevka",
            "size" => 0,
            "lat" => "55.545166",
            "lng" => "37.073220",
            "isCapital" => 0
        ]);
        $httpStatusCode = 200;

        $geoClient = $this->getGeoClientWithMockedHttpClient($jsonResponse, $httpStatusCode);

        $town = $geoClient->getTownById(33);

        $this->assertInstanceOf(Town::class, $town);
        $this->assertEquals("Апрелевка", $town->getName());

        // имитация ответа со статусом 401
        $httpStatusCode = 401;
        $geoClient = $this->getGeoClientWithMockedHttpClient($jsonResponse, $httpStatusCode);
        $this->expectException(UnauthorizedException::class);
        $geoClient->getTownById(33);
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