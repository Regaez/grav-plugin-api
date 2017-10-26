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

        $filter = null;

        if ( !empty($this->config->pages->fields) ) {
            $filter = $this->config->pages->fields;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
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

        $filter = null;

        if ( !empty($this->config->page->fields) ) {
            $filter = $this->config->page->fields;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
    }
}
