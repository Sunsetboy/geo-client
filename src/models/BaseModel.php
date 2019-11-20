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
        }
    }
}
