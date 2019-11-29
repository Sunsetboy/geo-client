<?php

namespace GeoServiceClient\models;

class Town extends BaseModel
{
    protected $id;
    protected $name;
    protected $regionId;
    protected $alias;
    protected $lat;
    protected $lng;
    protected $distance;
    protected $size;

    // Названия свойств, которые можно массово присвоить методом setAttributes
    protected $fillable = ['id', 'name', 'regionId', 'alias', 'lat', 'lng', 'distance', 'size'];

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
     * @return integer
     */
    public function getRegionId()
    {
        return $this->regionId;
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
}
