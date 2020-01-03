<?php

namespace GeoServiceClient;

use GeoServiceClient\exceptions\NotFoundException;
use GeoServiceClient\exceptions\UnauthorizedException;
use GeoServiceClient\models\Region;
use GeoServiceClient\models\Town;
use GeoServiceClient\models\Country;
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
     * @param array $params
     * @return Town
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getTownById($id, $params = []): Town
    {
        $responseJson = $this->request('GET', '/town/get/' . $id, $params);
        $responseArray = json_decode($responseJson, true);
        $town = new Town();
        $town->setAttributes($responseArray);

        return $town;
    }

    /**
     * @param string $alias
     * @param array $params
     * @return Town
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getTownByAlias($alias, $params = []): Town
    {
        $responseJson = $this->request('GET', '/town/alias/' . $alias, $params);
        $responseArray = json_decode($responseJson, true);
        $town = new Town();
        $town->setAttributes($responseArray[0]);

        return $town;
    }

    /**
     * @param integer $id
     * @param array $params
     * @return Town
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getRegionById($id, $params = []): Region
    {
        $responseJson = $this->request('GET', '/region/get/' . $id, $params);
        $responseArray = json_decode($responseJson, true);
        $region = new Region();
        $region->setAttributes($responseArray);

        return $region;
    }

    /**
     * @param string $alias
     * @param array $params
     * @return Region
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getRegionByAlias($alias, $params = []): Region
    {
        $responseJson = $this->request('GET', '/region/alias/' . $alias, $params);
        $responseArray = json_decode($responseJson, true);
        $region = new Region();
        $region->setAttributes($responseArray[0]);

        return $region;
    }

    /**
     * @param integer $id
     * @param array $params
     * @return Country
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getCountryById($id, $params = []): Country
    {
        $responseJson = $this->request('GET', '/country/get/' . $id, $params);
        $responseArray = json_decode($responseJson, true);
        $country = new Country();
        $country->setAttributes($responseArray);

        return $country;
    }

    /**
     * Возвращает массив ближайших городов к заданному городу
     * @param integer $townId
     * @param int $radius Радиус в километрах
     * @param int $limit Лимит выборки
     * @param array $params
     * @return Town[]
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getClosestTowns($townId, $radius = 100, $limit = 10, $params = []): array
    {
        $responseJson = $this->request('GET', '/town/closest/' . $townId . '/radius/' . $radius . '/limit/' . $limit, $params);
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
     * @param array $params
     * @return Town[]
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getTownsByIds($ids, $params = []): array
    {
        $getParams = ['ids' => implode(',', $ids)] + $params;
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
     * @param array $params
     * @return Town[]
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getTowns($limit = 10, $regionId = null, $countryId = null, $search = null, $params = []): array
    {
        $getParams = [];
        if ($limit) {
            $getParams['limit'] = $limit;
        }
        if ($regionId) {
            $getParams['region_id'] = $regionId;
        }
        if ($countryId) {
            $getParams['country_id'] = $countryId;
        }
        if ($search) {
            $getParams['search'] = $search;
        }

        $getParams += $params;

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
     * @param array $params
     * @return Region[]
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function getRegions($limit = 10, $countryId = null, $params = []): array
    {
        $getParams = [];
        if ($limit) {
            $getParams['limit'] = $limit;
        }
        if ($countryId) {
            $getParams['countryId'] = $countryId;
        }
        $getParams += $params;

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
