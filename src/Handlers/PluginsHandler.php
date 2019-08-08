<?php
namespace GravApi\Handlers;

use GravApi\Responses\Response;
use GravApi\Resources\PluginResource;
use GravApi\Resources\PluginCollectionResource;

/**
 * Class PluginsHandler
 * @package GravApi\Handlers
 */
class PluginsHandler extends BaseHandler
{
    public function getPlugins($request, $response, $args)
    {
        $plugins = $this->grav['plugins'];

        $resource = new PluginCollectionResource($plugins);

        return $response->withJson($resource->toJson());
    }

    public function getPlugin($request, $response, $args)
    {
        // Check all plugins against requested plugin id
        foreach ($this->grav['plugins'] as $p) {
            if ($p->name === $args['plugin']) {
                $resource = new PluginResource($p);
                return $response->withJson($resource->toJson());
            }
        }

        // If no matching plugin is found
        return $response->withJson(Response::notFound(), 404);
    }
}
