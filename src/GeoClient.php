<?php

namespace GeoServiceClient;

use GeoServiceClient\exceptions\UnauthorizedException;
use GeoServiceClient\models\Town;
use GuzzleHttp\Client;

class GeoClient
{
    /** @var Client */
    protected $httpClient;

    /**
     * Токен администратора. Должен отправляться в запросах на изменение данных
     * @var string
     */
    protected $adminToken;

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
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws UnauthorizedException
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
}