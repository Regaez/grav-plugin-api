<?php
namespace GravApi\Handlers;

use GravApi\Responses\Response;

class NotFoundHandler
{
    public function __invoke($request, $response)
    {
        return $response->withJson(Response::notFound(), 404);
    }
}
