<?php

namespace GeoServiceClient;

use GeoServiceClient\exceptions\NotFoundException;
use GeoServiceClient\exceptions\UnauthorizedException;
use GeoServiceClient\models\Region;
use GeoServiceClient\models\Town;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;

class GeoClient
{
    /** @var Client */
    protected $httpClient;

    /**
     * Токен администратора. Должен отправляться в запросах на изменение данных
     * @var string
     */
    protected $adminToken;

    /**
     * GeoClient constructor.
     * @param string $geoApiUrl
     * @param string|null $adminToken Нужен для действий, требующих аутентификации
     */
    public function __construct($geoApiUrl, $adminToken = null)
    {
        $this->httpClient = new Client([
            'base_uri' => $geoApiUrl,
        ]);

        $this->adminToken = $adminToken;
    }

    /**
     * @param string $method
     * @param string $route
     * @param array $queryParams
     * @param array $bodyParams
     * @param array $headers
     * @return StreamInterface
     * @throws GuzzleException
     * @throws UnauthorizedException
     * @throws NotFoundException
     */
    protected function request($method, $route, $queryParams = [], $bodyParams = [], $headers = [])
    {
        if ($this->adminToken) {
            $headers = array_merge($headers, ['X-Auth-Token' => $this->adminToken]);
        }

        $response = $this->httpClient->request($method, $route, [
            'query' => $queryParams,
            'form_params' => $bodyParams,
            'headers' => $headers,
        ]);

        if ($response->getStatusCode() == 401) {
            throw new UnauthorizedException();
        }

        if ($response->getStatusCode() == 404) {
            throw new NotFoundException();
        }

        return $response->getBody();
    }

    public function getTownById($id)
    {
        $responseJson = $this->request('GET', '/town/' . $id);
        $responseArray = json_decode($responseJson, true);
        $town = new Town();
        $town->setAttributes($responseArray);

        return $town;
    }

    public function getRegionById($id)
    {
        $responseJson = $this->request('GET', '/region/' . $id);
        $responseArray = json_decode($responseJson, true);
        $region = new Region();
        $region->setAttributes($responseArray);

        return $region;
    }

    /**
     * @param Client $httpClient
     * @return GeoClient
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }
}
