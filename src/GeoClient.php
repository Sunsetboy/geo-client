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
     * @param Client $httpClient
     * @return GeoClient
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
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

    /**
     * Возвращает объект города или выбрасывает исключение, если город не найден
     * @param integer $id
     * @return Town
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getTownById($id): Town
    {
        $responseJson = $this->request('GET', '/town/' . $id);
        $responseArray = json_decode($responseJson, true);
        $town = new Town();
        $town->setAttributes($responseArray);

        return $town;
    }

    /**
     * @param integer $id
     * @return Town
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getRegionById($id): Region
    {
        $responseJson = $this->request('GET', '/region/' . $id);
        $responseArray = json_decode($responseJson, true);
        $region = new Region();
        $region->setAttributes($responseArray);

        return $region;
    }

    /**
     * Возвращает массив ближайших городов к заданному городу
     * @param integer $townId
     * @param int $radius Радиус в километрах
     * @param int $limit Лимит выборки
     * @return Town[]
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getClosestTowns($townId, $radius = 100, $limit = 10): array
    {
        $responseJson = $this->request('GET', '/town/closest/' . $townId . '/radius/' . $radius . '/limit/' . $limit);
        $responseArray = json_decode($responseJson, true);

        $towns = [];
        foreach ($responseArray as $townInfo) {
            $town = new Town();
            $town->setAttributes($townInfo);
            $towns[] = $town;
        }

        return $towns;
    }

    /**
     * Возвращает массив городов согласно критериям поиска
     * @param int $limit
     * @param int|null $regionId
     * @param int|null $countryId
     * @return Town[]
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getTowns($limit = 10, $regionId = null, $countryId = null): array
    {
        $getParams = [];
        if ($limit) {
            $getParams['limit'] = $limit;
        }
        if ($regionId) {
            $getParams['regionId'] = $regionId;
        }
        if ($countryId) {
            $getParams['countryId'] = $countryId;
        }

        $responseJson = $this->request('GET', '/town/get/', $getParams);
        $responseArray = json_decode($responseJson, true);

        $towns = [];
        foreach ($responseArray as $townInfo) {
            $town = new Town();
            $town->setAttributes($townInfo);
            $towns[] = $town;
        }

        return $towns;
    }

    public function getRegions($limit = 10, $countryId = null): array
    {
        $getParams = [];
        if ($limit) {
            $getParams['limit'] = $limit;
        }
        if ($countryId) {
            $getParams['countryId'] = $countryId;
        }

        $responseJson = $this->request('GET', '/region/get/', $getParams);
        $responseArray = json_decode($responseJson, true);

        $towns = [];
        foreach ($responseArray as $townInfo) {
            $town = new Town();
            $town->setAttributes($townInfo);
            $towns[] = $town;
        }

        return $towns;
    }
}
