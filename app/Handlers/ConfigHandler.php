<?php
namespace GravApi\Handlers;

use Grav\Common\Config\ConfigFileFinder;
use Grav\Common\Config\CompiledConfig;
use \GravApi\Resources\ConfigCollectionResource;
use \GravApi\Resources\ConfigResource;

/**
 * Class ConfigHandler
 * @package GravApi\Handlers
 */
class ConfigHandler extends BaseHandler
{
    public function getConfigs($request, $response, $args) {

        $configs = [];

        // Find all the root config files
        $this->finder = new ConfigFileFinder;
        $location = $this->grav['locator']->findResources('config://');
        $configFiles = $this->finder->listFiles($location, '|\.yaml$|', 0);

        // Retrieve fields of each config file
        foreach ($configFiles as $name => $files) {
            $configs[$name] = $this->grav['config']->get($name);
        }

        $resource = new ConfigCollectionResource($configs);

        $filter = [];

        if ( !empty($this->config->configs->ignore_files) ) {
            $filter = $this->config->configs->ignore_files;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
    }

    public function getConfig($request, $response, $args) {

        $name = $args['config'];

        // We first pass it through the ConfigCollection to check our ignore filter, e.g. can't access 'security'
        $collection = new ConfigCollectionResource([
            $name => $this->grav['config']->get($name)
        ]);

        $collectionFilter = [];
        if ( !empty($this->config->configs->ignore_files) ) {
            $collectionFilter = $this->config->configs->ignore_files;
        }
        $config = $collection->toJson($collectionFilter);

        if (!$config) {
            return $response->withStatus(404)
                            ->withHeader('Content-Type', 'text/html')
                            ->write('Page not found');
        }

        // Access to config file is allowed, now we can proceed
        $resource = new ConfigResource($config);

        $filter = null;

        if ( !empty($this->config->config->fields) ) {
            $filter = $this->config->config->fields;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
    }
}
