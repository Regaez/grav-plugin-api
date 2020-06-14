<?php
namespace GravApi\Handlers;

use GravApi\Responses\Response;
use GravApi\Helpers\ConfigHelper;
use GravApi\Helpers\ArrayHelper;
use GravApi\Resources\ConfigResource;
use GravApi\Resources\ConfigCollectionResource;
use GravApi\Config\Constants;
use GravApi\Models\ConfigModel;
use RocketTheme\Toolbox\Event\Event;
use RocketTheme\Toolbox\File\YamlFile;

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
            return $response->withJson(Response::badRequest('No config `id` given!'), 400);
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

    public function updateConfig($request, $response, $args)
    {
        if (!isset($args['config'])) {
            return $response->withJson(Response::badRequest('No config `id` given!'), 400);
        }

        $existingConfig = ConfigHelper::loadConfig($args['config']);

        // If the config doesn't exist, OR it is present on the filter list
        // (i.e. we don't want to allow user access to it)
        if (!$existingConfig) {
            return $response->withJson(Response::notFound(), 404);
        }

        $parsedBody = $request->getParsedBody();

        // Merge the existing config with the new settings
        $data = ArrayHelper::merge(
            $existingConfig->data,
            $parsedBody
        );

        $configModel = new ConfigModel($args['config'], $data);

        // Save the updates to file
        $filename = 'config://' . $configModel->id . '.yaml';
        $file = YamlFile::instance(
            $this->grav['locator']->findResource($filename, true, true)
        );
        $file->save($configModel->data);
        $file->free();

        // Reload the site config after changes are saved
        $this->grav['config']->reload();

        $resource = new ConfigResource($configModel);

        $this->grav->fireEvent(Constants::EVENT_ON_API_CONFIG_UPDATE, new Event(['config' => $configModel->data]));

        return $response->withJson($resource->toJson());
    }
}
