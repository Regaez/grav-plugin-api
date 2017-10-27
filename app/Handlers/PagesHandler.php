<?php
namespace GravApi\Handlers;

use Grav\Common\Filesystem\Folder;
use GravApi\Responses\Response;
use GravApi\Resources\PageResource;
use GravApi\Resources\PageCollectionResource;
use GravApi\Helpers\PageHelper;

/**
 * Class PagesHandler
 * @package GravApi\Handlers
 */
class PagesHandler extends BaseHandler
{
    public function getPages($request, $response, $args)
    {
        $collection = $this->grav['pages']->all();

        $resource = new PageCollectionResource($collection);

        $filter = null;

        if ( !empty($this->config->pages->fields) ) {
            $filter = $this->config->pages->fields;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
    }

    public function getPage($request, $response, $args)
    {
        $route = "/{$request->getAttribute('page')}";
        $page = $this->grav['pages']->find($route);

        if (!$page) {
            return $response->withJson(Response::NotFound(), 404);
        }

        $resource = new PageResource($page);

        $filter = null;

        if ( !empty($this->config->page->fields) ) {
            $filter = $this->config->page->fields;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
    }

    public function newPage($request, $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        if ( empty($parsedBody['route']) ) {
            return $response->withJson(Response::BadRequest('You must provide a `route` field!'), 400);
        }

        $route = $parsedBody['route'];
        $existingPage = $this->grav['pages']->find($route);

        if ($existingPage) {
            return $response->withJson(Response::ResourceExists(), 403);
        }

        $template = !empty($parsedBody['template']) ? $parsedBody['template'] : 'default';

        $helper = new PageHelper($route, $template);

        try {
            $page = $helper->getOrCreatePage();

            // Add frontmatter to our page
            if (!empty($parsedBody['header']) ) {
                if ( !is_array($parsedBody['header']) ) {
                    throw new \Exception("Field `header` must be valid JSON.", 1);
                }

                $page->header($parsedBody['header']);
            }

            // Add content to our page
            if (!empty($parsedBody['content']) ) {
                $page->content($parsedBody['content']);
            }

            // Save the page with the new header/content fields
            $page->save();

        } catch(\Exception $e) {
            // rollback
            $success = Folder::delete($helper->page->path());

            return $response->withJson(Response::BadRequest($e->getMessage()), 400);
        }

        // Use our resource to return the filtered page
        $resource = new PageResource($page);

        $filter = null;

        if ( !empty($this->config->page->fields) ) {
            $filter = $this->config->page->fields;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
    }
}
