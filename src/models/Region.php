<?php

namespace GeoServiceClient\models;

class Region extends BaseModel
{
    /** @var integer */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $alias;
    /** @var Country */
    protected $country;

    // Названия свойств, которые можно массово присвоить
    protected $fillable = ['id', 'name', 'alias'];

    /**
     * @return int
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
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     * @return Region
     */
    public function setCountry(Country $country): Region
    {
        $this->country = $country;
        return $this;
    }
}
