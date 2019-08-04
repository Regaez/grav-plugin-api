<?php
namespace GravApi\Handlers;

use GravApi\Responses\Response;
use GravApi\Resources\PluginResource;

/**
 * Class PluginsHandler
 * @package GravApi\Handlers
 */
class PluginsHandler extends BaseHandler
{
    public function getPlugins($request, $response, $args)
    {

        $plugins = $this->grav['plugins'];

        $filter = null;

        if (!empty($this->config->plugins->fields)) {
            $filter = $this->config->plugins->fields;
        }

        $data = [];

        foreach ($plugins as $plugin) {
            $resource = new PluginResource($plugin);
            $data[$plugin->name] = $resource->toJson($filter);
        }

        return $response->withJson($data);
    }

    public function getPlugin($request, $response, $args)
    {

        $plugin = null;

        foreach ($this->grav['plugins'] as $p) {
            if ($p->name === $args['plugin']) {
                $plugin = $p;
            }
        }

        if (!$plugin) {
            return $response->withJson(Response::notFound(), 404);
        }

        $resource = new PluginResource($plugin);

        $filter = null;

        if (!empty($this->config->plugin->fields)) {
            $filter = $this->config->plugin->fields;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
    }
}
