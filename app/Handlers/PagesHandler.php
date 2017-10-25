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

        $page = $this->grav['pages']->find('/'. $args['page']);

        $resource = new PageResource($page);

        return $response->withJson($resource->toJson());
    }
}
