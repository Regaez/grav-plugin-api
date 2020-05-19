<?php
namespace GravApi\Handlers;

use GravApi\Responses\Response;
use GravApi\Helpers\ConfigHelper;
use GravApi\Resources\ConfigResource;
use GravApi\Resources\ConfigCollectionResource;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class ConfigHandler
 * @package GravApi\Handlers
 */
class ConfigHandler extends BaseHandler
{
    public function getConfigs($request, $response, $args)
    {
        $configs = ConfigHelper::loadConfigs();

        $resource = new ConfigCollectionResource($configs);

        $this->grav->fireEvent(Constants::EVENT_ON_API_CONFIG_GET_ALL, new Event(['configs' => $configs]));

        return $response->withJson($resource->toJson());
    }

    public function getConfig($request, $response, $args)
    {
        if (!isset($args['config'])) {
            return $response->withJson(Response::badRequest('No config `id   given!'), 400);
        }

        $config = ConfigHelper::loadConfig($args['config']);

        // If the config doesn't exist, OR it is present on the filter list
        // (i.e. we don't want to allow user access to it)
        if (!$config) {
            return $response->withJson(Response::notFound(), 404);
        }

        $resource = new ConfigResource($config);

        $this->grav->fireEvent(Constants::EVENT_ON_API_CONFIG_GET, new Event(['config' => $config]));

        return $response->withJson($resource->toJson());
    }
}
