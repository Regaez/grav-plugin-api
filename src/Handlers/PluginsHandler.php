<?php
namespace GravApi\Handlers;

use GravApi\Responses\Response;
use GravApi\Resources\PluginResource;
use GravApi\Resources\PluginCollectionResource;
use GravApi\Helpers\ArrayHelper;
use GravApi\Helpers\PluginHelper;
use Grav\Common\Data\ValidationException;
use RocketTheme\Toolbox\File\YamlFile;
use GravApi\Config\Constants;
use RocketTheme\Toolbox\Event\Event;

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

        $this->grav->fireEvent(Constants::EVENT_ON_API_PLUGIN_GET_ALL, new Event(['plugins' => $plugins]));

        return $response->withJson($resource->toJson());
    }

    public function getPlugin($request, $response, $args)
    {
        $plugin = PluginHelper::find($args['plugin']);

        // If no matching plugin is found
        if (!$plugin) {
            return $response->withJson(Response::notFound(), 404);
        }

        $resource = new PluginResource($plugin);

        $this->grav->fireEvent(Constants::EVENT_ON_API_PLUGIN_GET, new Event(['plugin' => $plugin]));

        return $response->withJson($resource->toJson());
    }

    public function updatePlugin($request, $response, $args)
    {
        $plugin = PluginHelper::find($args['plugin']);

        // If no matching plugin is found
        if (!$plugin) {
            return $response->withJson(Response::notFound(), 404);
        }

        $parsedBody = $request->getParsedBody();

        try {
            // Merge the existing config with the new settings
            $config = ArrayHelper::merge(
                $plugin->config(),
                $parsedBody
            );
            // Validate the config against the plugin blueprint
            $plugin->getBlueprint()->validate($config);
        } catch (ValidationException $e) {
            return $response->withJson(
                Response::badRequest($e->getMessage()),
                400
            );
        }

        // Save the updates to file
        $filename = 'config://plugins/' . $plugin->name . '.yaml';
        $file = YamlFile::instance(
            $this->grav['locator']->findResource($filename, true, true)
        );
        $file->save($config);
        $file->free();

        // Reload the site config after changes are saved
        $this->grav['config']->reload();
        $plugin = PluginHelper::find($args['plugin']);

        $resource = new PluginResource($plugin);

        $this->grav->fireEvent(Constants::EVENT_ON_API_PLUGIN_UPDATE, new Event(['plugin' => $plugin]));

        return $response->withJson($resource->toJson());
    }
}
