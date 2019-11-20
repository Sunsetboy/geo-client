<?php

namespace GeoServiceClient\exceptions;

class UnauthorizedException extends \Exception
{
    protected $message = 'Authentication required for POST request. Provide a valid token.';
}
