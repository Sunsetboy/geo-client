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
            'alias' => 'jewish',
            'country' => [
                'id' => 10,
                'name' => 'Нидерланды',
                'alias' => 'netherlands',
            ],
        ];

        $region = new Region();
        $region->setAttributes($regionAttributes);

        $this->assertEquals($regionAttributes['id'], $region->getId());
        $this->assertEquals($regionAttributes['name'], $region->getName());
        $this->assertEquals($regionAttributes['alias'], $region->getAlias());
        $this->assertEquals($regionAttributes['country']['name'], $region->getCountry()->getName());
    }
}
