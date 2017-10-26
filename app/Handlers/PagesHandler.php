<?php
namespace GravApi\Handlers;

use \GravApi\Resources\PageResource;
use \GravApi\Resources\PageCollectionResource;

/**
 * Class PagesHandler
 * @package GravApi\Handlers
 */
class PagesHandler extends BaseHandler
{
    public function getPages($request, $response, $args) {

        $collection = $this->grav['pages']->all();

        $resource = new PageCollectionResource($collection);

        return $response->withJson($resource->toJson());
    }

    public function getPage($request, $response, $args) {

        $route = "/{$request->getAttribute('page')}";
        $page = $this->grav['pages']->find($route);

        if (!$page) {
            return $response->withStatus(404)
                            ->withHeader('Content-Type', 'text/html')
                            ->write('Page not found');
        }

        $resource = new PageResource($page);

        return $response->withJson($resource->toJson());
    }
}
