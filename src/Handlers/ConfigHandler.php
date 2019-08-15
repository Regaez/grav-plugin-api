<?php
namespace GravApi\Handlers;

use Grav\Common\Config\ConfigFileFinder;
use Grav\Common\Config\CompiledConfig;
use GravApi\Responses\Response;
use GravApi\Resources\ConfigResource;
use GravApi\Resources\ConfigCollectionResource;

/**
 * Class ConfigHandler
 * @package GravApi\Handlers
 */
class ConfigHandler extends BaseHandler
{
    /**
     * Returns a Config object that can be passed to the ConfigResource
     *
     * @param  string $id
     * @param  array  $data
     * @return object Contains properties: id, data
     */
    public function createConfig(string $id, array $data)
    {
        return (object) [
            'id' => $id,
            'data' => $data
        ];
    }

    public function getConfigs($request, $response, $args)
    {
        $configs = [];

        // Find all the root config files
        $location = $this->grav['locator']->findResources('config://');
        $configFiles = (new ConfigFileFinder)->listFiles($location);

        // Retrieve fields of each config file
        foreach ($configFiles as $name => $value) {
            $data = $this->grav['config']->get($name);
            if ($data) {
                $configs[] = $this->createConfig($name, $data);
            }
        }

        $resource = new ConfigCollectionResource($configs);

        return $response->withJson($resource->toJson());
    }

    public function getConfig($request, $response, $args)
    {
        $name = $args['config'];

        $data = $this->grav['config']->get($name);

        // If the Config doesn't exist, OR it is present on the filter list
        // (i.e. we don't want to allow user access to it)
        if (!$data || in_array($name, ConfigCollectionResource::getFilter())) {
            return $response->withJson(Response::notFound(), 404);
        }

        $config = $this->createConfig($name, $data);

        $resource = new ConfigResource($config);

        return $response->withJson($resource->toJson());
    }
}
