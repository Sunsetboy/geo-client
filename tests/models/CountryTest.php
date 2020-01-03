<?php

namespace models;

use GeoServiceClient\models\Country;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    public function testMassiveSetterAndGetters()
    {
        $countryAttributes = [
            'id' => 1000,
            'name' => 'Сингапур',
            'alias' => 'singapore',
        ];

        $country = new Country();
        $country->setAttributes($countryAttributes);

        $this->assertEquals($countryAttributes['id'], $country->getId());
        $this->assertEquals($countryAttributes['name'], $country->getName());
        $this->assertEquals($countryAttributes['alias'], $country->getAlias());
    }
}
