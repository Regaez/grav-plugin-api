<?php
namespace GravApi\Handlers;

use Grav\Common\Filesystem\Folder;
use GravApi\Config\Config;
use GravApi\Config\Constants;
use GravApi\Responses\Response;
use GravApi\Resources\PageResource;
use GravApi\Resources\PageCollectionResource;
use GravApi\Helpers\PageHelper;
use GravApi\Helpers\ArrayHelper;
use GravApi\Helpers\AuthHelper;
use GravApi\Helpers\TaxonomyHelper;

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

        return $response->withJson($resource->toJson());
    }

    public function getPage($request, $response, $args)
    {
        if (!isset($args['page'])) {
            return $response->withJson(Response::badRequest('You must provide an `id` for the page!'), 400);
        }

        $route = "/{$args['page']}";
        $page = $this->grav['pages']->find($route);

        if (!$page) {
            return $response->withJson(Response::notFound(), 404);
        }

        // If auth is enabled, we need to check if user has
        // one of the custom taxonomy roles
        if (Config::instance()->pages->get->auth) {
            $user = $request->getAttribute('user');

            // Compare user's access roles against the available
            // taxonomy roles for the page. User might also have a
            // generic read role, so we need to check again for that.
            $hasRole = AuthHelper::checkRoles($user, array_merge(
                [Constants::ROLE_PAGES_READ],
                TaxonomyHelper::getPageRoles($page)
            ));

            if (!$hasRole) {
                return $response->withJson(Response::unauthorized(), 401);
            }
        }

        $resource = new PageResource($page);

        return $response->withJson($resource->toJson());
    }

    public function findPages($request, $response, $args)
    {
        $parsedBody = $request->getParsedBody();
        $operation = $parsedBody['operation'] ?? 'or';
        $filter = $parsedBody['taxonomyFilter'] ?? array();
        $collection = $this->grav['taxonomy']->findTaxonomy($filter, strtolower($operation));

        $resource = new PageCollectionResource($collection);

        return $response->withJson($resource->toJson());
    }

    public function newPage($request, $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        if (empty($parsedBody['route'])) {
            return $response->withJson(Response::badRequest('You must provide a `route` field!'), 400);
        }

        $route = $parsedBody['route'];
        $existingPage = $this->grav['pages']->find($route);

        // if existingPage is a directory, we can still create a file, so check if isPage
        if ($existingPage && $existingPage->isPage()) {
            return $response->withJson(Response::resourceExists(), 403);
        }

        $template = !empty($parsedBody['template']) ? $parsedBody['template'] : 'default';

        // Our helper is used to create a page in new directories
        $helper = new PageHelper($route, $template);

        try {
            // if existingPage evals true, it means a directory
            // already exists, we just need to save the file
            $page = $existingPage ?: $helper->getOrCreatePage();

            // Our Helper will set a template when creating a new page
            // but we set it here too in case we are using an existing dir 'page'
            $page->name($helper->getFilename());

            // Add frontmatter to our page
            if (!empty($parsedBody['header'])) {
                if (!is_array($parsedBody['header'])) {
                    throw new \Exception("Field `header` must be valid JSON.", 1);
                }

                $page->header($parsedBody['header']);
            }

            // Add content to our page
            if (!empty($parsedBody['content'])) {
                $page->content($parsedBody['content']);
            }

            if (!empty($parsedBody['order'])) {
                // Move the page we just initialised with PageHelper
                // so that we can set the desired page order
                $page->move($page->parent());
                $page->order($parsedBody['order']);
            }

            // Save the page with the new header/content fields
            $page->save();
        } catch (\Exception $e) {
            // rollback
            $success = Folder::delete($helper->page->path());

            return $response->withJson(Response::badRequest($e->getMessage()), 400);
        }

        // Use our resource to return the filtered page
        $resource = new PageResource($page);

        return $response->withJson($resource->toJson());
    }

    public function deletePage($request, $response, $args)
    {
        if (!isset($args['page'])) {
            return $response->withJson(Response::badRequest('You must provide an `id` for the page!'), 400);
        }

        $route = "/{$args['page']}";
        $page = $this->grav['pages']->find($route);

        if (!$page || !$page->exists()) {
            return $response->withJson(Response::notFound(), 404);
        }

        // if the requested route has non-modular children,
        // we just delete the route's markdown file, keeping the directory
        if (0 < count($page->children()->nonModular())) {
            $page->file()->delete();
        } else {
            Folder::delete($page->path());

            // since this page has no children, we can clean up the unused directories too

            $child = $page;
            $parentRoute = dirname($page->route());
            // recursively check parent directories for files, and delete them if empty
            while ($parentRoute !== '') {
                $parent = $this->grav['pages']->find($parentRoute);

                // if we hit the root, stop
                if ($parent === null) {
                    break;
                }

                // Get the parents children, minus the child we just deleted
                $filteredChildren = $parent->children()->remove($child);

                // if the parent directory exists, or has children, we should stop
                if ($parent->isPage() || 0 < count($filteredChildren->toArray())) {
                    break;
                }

                // set this parent as the next child to delete
                $child = $parent;
                // delete the folder
                Folder::delete($parent->path());
                $parentRoute = dirname($parentRoute);
            }
        }

        return $response->withStatus(204);
    }

    public function updatePage($request, $response, $args)
    {
        if (!isset($args['page'])) {
            return $response->withJson(Response::badRequest('You must provide an `id` for the page!'), 400);
        }

        $route = "/{$args['page']}";
        $page = $this->grav['pages']->find($route);

        if (!$page || !$page->exists()) {
            return $response->withJson(Response::notFound(), 404);
        }

        $parsedBody = $request->getParsedBody();

        $template = isset($parsedBody['template'])
            ? $parsedBody['template']
            : '';

        if (empty($parsedBody['route'])) {
            return $response->withJson(Response::badRequest('You must provide a `route` field!'), 400);
        }

        // update the page content
        if (!empty($parsedBody['content'])) {
            $page->content($parsedBody['content']);
        }

        // create new helper for updating header and template
        $helper = new PageHelper($route, $template);

        // update the page header
        if (!empty($parsedBody['header'])) {
            if (!is_array($parsedBody['header'])) {
                $message = "Field `header` must be valid JSON.";
                return $response->withJson(
                    Response::badRequest($message),
                    400
                );
            }

            $updatedHeader = ArrayHelper::merge(
                $page->header(),
                $parsedBody['header']
            );

            $page->header($updatedHeader);
        }

        // update the page template
        if (!empty($parsedBody['template'])) {
            // we need to trigger a fake 'move'
            // (i.e. to the same parent)
            // otherwise a new file will be made
            // instead of renaming the existing one
            $page->move($page->parent());

            // sets the file to use our new template
            $page->name($helper->getFilename());
        }

        // We need to be able to pass `false` to remove page order
        if (isset($parsedBody['order'])) {
            // We have to "move" the page, otherwise
            // the old page order folder will remain
            $page->move($page->parent());
            $page->order($parsedBody['order']);
        }

        // save the changes to the file
        // (this is when the 'move' would actually happen)
        $page->save();

        // Use our resource to return the updated page
        $resource = new PageResource($page);

        return $response->withJson($resource->toJson());
    }
}
