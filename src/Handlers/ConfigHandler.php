<?php
namespace GravApi\Handlers;

use GravApi\Responses\Response;
use GravApi\Helpers\ConfigHelper;
use GravApi\Resources\ConfigResource;
use GravApi\Resources\ConfigCollectionResource;

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

        return $response->withJson($resource->toJson());
    }

    public function getConfig($request, $response, $args)
    {
        $config = ConfigHelper::loadConfig($args['config']);

        // If the config doesn't exist, OR it is present on the filter list
        // (i.e. we don't want to allow user access to it)
        if (!$config) {
            return $response->withJson(Response::notFound(), 404);
        }

        $resource = new ConfigResource($config);

        return $response->withJson($resource->toJson());
    }
}
