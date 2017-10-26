<?php
namespace GravApi\Responses;

/**
 * Class Response
 * @package GravApi\Responses
 */
class Response
{
    // URL to API documentation
    protected static $documentation = 'https://github.com/Regaez/grav-plugin-api/wiki';

    public static function NotFound()
    {
        return  [
            'message' => 'Resource not found',
            'documentation' => self::$documentation
        ];
    }
}
