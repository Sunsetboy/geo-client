<?php


use GeoServiceClient\GeoClient;
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
        $httpClientMock = $this->createMock(Client::class);
        $httpClientResponseMock = $this->createMock(Response::class);
        $httpClientResponseMock->method('getBody')->willReturn($jsonResponse);
        $geoClient = new GeoClient('http://test.local');
        $geoClient->setHttpClient($httpClientMock);

        $httpClientMock->method('request')->willReturn($httpClientResponseMock);
        $town = $geoClient->getTownById(33);

        $this->assertEquals("Апрелевка", $town->getName());
    }
}