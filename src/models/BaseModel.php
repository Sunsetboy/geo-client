<?php


namespace GeoServiceClient\models;


class BaseModel
{
    protected $fillable = [];

    /**
     * Наполняет свойства объекта значениями из массива. Наполняются только свойства, перечисленные в свойстве $fillable
     * @param array $attributes массив вида [prop_name => prop_value]
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $name => $value) {
            if (property_exists($this, $name) && in_array($name, $this->fillable)) {
                $this->$name = $value;
            }

            if ($name == 'region' && method_exists($this, 'setRegion')) {
                $region = new Region();
                $region->setAttributes($value);
                $this->setRegion($region);
            }

            if ($name == 'country' && method_exists($this, 'setCountry')) {
                $country = new Country();
                $country->setAttributes($value);
                $this->setCountry($country);
            }
        }
    }
}
