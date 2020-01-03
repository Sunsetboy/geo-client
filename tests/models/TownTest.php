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
            'alias' => 'Kogalym',
            'lat' => 56.9984,
            'lng' => -100.893201,
            'isCapital' => 0,
            'size' => 1000,
            'distance' => 10,
            'region' => [
                'id' => 18,
                'name' => 'Северная Голландия',
                'alias' => 'nothern-holland',
            ],
            'country' => [
                'id' => 10,
                'name' => 'Нидерланды',
                'alias' => 'netherlands',
            ],
        ];

        $town = new Town();
        $town->setAttributes($townAttributes);

        $this->assertEquals($townAttributes['id'], $town->getId());
        $this->assertEquals($townAttributes['name'], $town->getName());
        $this->assertEquals($townAttributes['region']['name'], $town->getRegion()->getName());
        $this->assertEquals($townAttributes['country']['name'], $town->getCountry()->getName());
        $this->assertEquals($townAttributes['alias'], $town->getAlias());
        $this->assertEquals($townAttributes['lat'], $town->getLat());
        $this->assertEquals($townAttributes['lng'], $town->getLng());
        $this->assertEquals($townAttributes['size'], $town->getSize());
        $this->assertEquals($townAttributes['isCapital'], $town->getIsCapital());
        $this->assertEquals($townAttributes['distance'], $town->getDistance());
    }
}
