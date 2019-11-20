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
    /** @var integer */
    protected $countryId;

    // Названия свойств, которые можно массово присвоить
    protected $fillable = ['id', 'name', 'countryId', 'alias'];

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
     * @return int
     */
    public function getCountryId()
    {
        return $this->countryId;
    }
}
