<?php

use GeoServiceClient\models\Town;
use PHPUnit\Framework\TestCase;

class TownTest extends TestCase
{
    public function testMassiveSetterAndGetters()
    {
        $townAttributes = [
            'id' => 10,
            'name' => 'Когалым',
            'regionId' => 22,
            'alias' => 'Kogalym',
            'lat' => 56.9984,
            'lng' => -100.893201,
        ];

        $town = new Town();
        $town->setAttributes($townAttributes);

        $this->assertEquals($townAttributes['id'], $town->getId());
        $this->assertEquals($townAttributes['name'], $town->getName());
        $this->assertEquals($townAttributes['regionId'], $town->getRegionId());
        $this->assertEquals($townAttributes['alias'], $town->getAlias());
        $this->assertEquals($townAttributes['lat'], $town->getLat());
        $this->assertEquals($townAttributes['lng'], $town->getLng());
    }
}
