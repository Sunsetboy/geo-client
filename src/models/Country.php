<?php

namespace GeoServiceClient\models;

class Country extends BaseModel
{
    /** @var integer */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $alias;

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
}
