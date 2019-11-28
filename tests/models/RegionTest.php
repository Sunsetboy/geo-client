<?php

use GeoServiceClient\models\Region;
use PHPUnit\Framework\TestCase;

class RegionTest extends TestCase
{
    public function testMassiveSetterAndGetters()
    {
        $regionAttributes = [
            'id' => 10,
            'name' => 'Еврейская АО',
            'countryId' => 2,
            'alias' => 'jewish',
        ];

        $region = new Region();
        $region->setAttributes($regionAttributes);

        $this->assertEquals($regionAttributes['id'], $region->getId());
        $this->assertEquals($regionAttributes['name'], $region->getName());
        $this->assertEquals($regionAttributes['countryId'], $region->getCountryId());
        $this->assertEquals($regionAttributes['alias'], $region->getAlias());
    }
}
