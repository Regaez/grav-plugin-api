<?php
namespace GravApi\Responses;

/**
 * Class Response
 * @package GravApi\Responses
 */
class Response
{
    public static function NotFound()
    {
        $response = new NotFoundResponse();
        return $response->get();
    }

    public static function Unauthorized()
    {
        $response = new UnauthorizedResponse();
        return $response->get();
    }
}
