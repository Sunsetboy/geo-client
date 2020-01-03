<?php

namespace GeoServiceClient\models;

class Town extends BaseModel
{
    protected $id;
    protected $name;
    protected $alias;
    protected $lat;
    protected $lng;
    protected $distance;
    protected $size;
    protected $isCapital;
    /** @var Country */
    protected $country;
    /** @var Region */
    protected $region;

    // Названия свойств, которые можно массово присвоить методом setAttributes
    protected $fillable = [
        'id', 'name', 'alias',
        'lat', 'lng', 'distance', 'size', 'isCapital'
    ];

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return float|null
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return float|null
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return integer
     */
    public function getIsCapital()
    {
        return $this->isCapital;
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @return Region
     */
    public function getRegion(): Region
    {
        return $this->region;
    }

    /**
     * @param Country $country
     * @return Town
     */
    public function setCountry(Country $country): Town
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @param Region $region
     * @return Town
     */
    public function setRegion(Region $region): Town
    {
        $this->region = $region;
        return $this;
    }
}
