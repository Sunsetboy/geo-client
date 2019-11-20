<?php

namespace GeoServiceClient;

use GuzzleHttp\Client;

class GeoClient
{
    /** @var Client */
    protected $httpClient;

    public function __construct($geoApiUrl)
    {
        $this->httpClient = new Client([

        ]);
    }

    /**
     *
     */
    protected function request()
    {

    }

    public function getTownById()
    {

    }
}
