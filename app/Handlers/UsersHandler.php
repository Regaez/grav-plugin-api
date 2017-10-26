<?php
namespace GravApi\Handlers;

use GravApi\Resources\UserResource;
use Symfony\Component\Yaml\Yaml;

/**
 * Class PagesHandler
 * @package GravApi\Handlers
 */
class UsersHandler extends BaseHandler
{
    public function getUsers($request, $response, $args) {

        $users = [];

        $files = (array) glob($this->grav['locator']->findResource("account://") . '/*.yaml');

        if (!$files) {
            return $response->withStatus(404)
                            ->withHeader('Content-Type', 'text/html')
                            ->write('Page not found');
        }

        foreach ($files as $file) {
            $details = array_merge(
                array('username' => basename($file, '.yaml')),
                Yaml::parse($file)
            );
            $resource = new UserResource($details);
            $users[] = $resource->toJson();
        }

        return $response->withJson($users);
    }

    public function getUser($request, $response, $args) {

        $file = $this->grav['locator']->findResource("account://") . "/{$args['user']}.yaml";

        if (!file_exists($file)) {
            return $response->withStatus(404)
                            ->withHeader('Content-Type', 'text/html')
                            ->write('Page not found');
        }

        $resource = new UserResource(array_merge(
            array('username' => basename($file, '.yaml')),
            Yaml::parse($file)
        ));

        return $response->withJson($resource->toJson());
    }
}
