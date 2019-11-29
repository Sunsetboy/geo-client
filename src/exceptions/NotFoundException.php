<?php

namespace GeoServiceClient\exceptions;

class NotFoundException extends \Exception
{
    protected $message = 'Object not found';
}
