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
     * Получает массив городов по списку их id
     * @param integer[] $ids
     * @return Town[]
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getTownsByIds($ids): array
    {
        $getParams = ['ids' => implode(',', $ids)];
        $responseJson = $this->request('GET', '/town/list-by-id', $getParams);
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
     * @param string|null $search
     * @return Town[]
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getTowns($limit = 10, $regionId = null, $countryId = null, $search = null): array
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
        if ($search) {
            $getParams['search'] = $search;
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

    /**
     * @param int $limit
     * @param int|null $countryId
     * @return Region[]
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
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

        $regions = [];
        foreach ($responseArray as $regionInfo) {
            $region = new Region();
            $region->setAttributes($regionInfo);
            $regions[] = $region;
        }

        return $regions;
    }

    /**
     * @param integer $id
     * @param array $attributes
     * @return Town
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function updateTown($id, $attributes): Town
    {
        $responseJson = $this->request('POST', '/town/' . $id, null, $attributes);
        $responseArray = json_decode($responseJson, true);
        $town = new Town();
        $town->setAttributes($responseArray);

        return $town;
    }

    /**
     * @param integer $id
     * @param array $attributes
     * @return Region
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function updateRegion($id, $attributes): Region
    {
        $responseJson = $this->request(
            'POST',
            '/region/' . $id,
            null,
            $attributes
        );
        $responseArray = json_decode($responseJson, true);
        $region = new Region();
        $region->setAttributes($responseArray);

        return $region;
    }
}
