<?php
namespace GravApi\Responses;

/**
 * Class Response
 * @package GravApi\Responses
 */
class Response
{
    public static function notFound()
    {
        $response = new NotFoundResponse();
        return $response->get();
    }

    public static function unauthorized()
    {
        $response = new UnauthorizedResponse();
        return $response->get();
    }

    public static function resourceExists()
    {
        $response = new ResourceExistsResponse();
        return $response->get();
    }

    public static function badRequest($msg)
    {
        $response = new BadRequestResponse($msg);
        return $response->get();
    }
}
