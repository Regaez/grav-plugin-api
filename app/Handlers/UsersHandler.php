<?php
namespace GravApi\Handlers;

use GravApi\Resources\UserResource;
use GravApi\Responses\Response;
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
            return $response->withJson(Response::NotFound(), 404);
        }

        $filter = null;

        if ( !empty($this->config->users->fields) ) {
            $filter = $this->config->users->fields;
        }

        foreach ($files as $file) {
            $username = basename($file, '.yaml');
            $details = array_merge(
                array('username' => $username),
                Yaml::parse($file)
            );
            $resource = new UserResource($details);
            $users[$username] = $resource->toJson($filter);
        }

        return $response->withJson($users);
    }

    public function getUser($request, $response, $args) {

        $file = $this->grav['locator']->findResource("account://") . "/{$args['user']}.yaml";

        if (!file_exists($file)) {
            return $response->withJson(Response::NotFound(), 404);
        }

        $resource = new UserResource(array_merge(
            array('username' => basename($file, '.yaml')),
            Yaml::parse($file)
        ));

        $filter = null;

        if ( !empty($this->config->user->fields) ) {
            $filter = $this->config->user->fields;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
    }
}
