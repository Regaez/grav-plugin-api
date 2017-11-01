<?php
namespace GravApi\Handlers;

use GravApi\Resources\UserResource;
use GravApi\Responses\Response;
use Grav\Common\User\User;
use Grav\Common\Inflector;
use Grav\Common\File\CompiledYamlFile;
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
            $users[] = $resource->toJson($filter, true);
        }

        $data = [
            'items' => $users,
            'meta' => [
                'count' => count($users)
            ]
        ];

        return $response->withJson($data);
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

    public function newUser($request, $response, $args) {
        $parsedBody = $request->getParsedBody();

        if ( empty($parsedBody['username']) ) {
            return $response->withJson(Response::BadRequest('You must provide a `username` field!'), 400);
        }

        if ( empty($parsedBody['password']) ) {
            return $response->withJson(Response::BadRequest('You must provide a `password` field!'), 400);
        }

        $inflector = new Inflector();
        $username = $inflector->underscorize($parsedBody['username']);

        $file = $this->grav['locator']->findResource("account://") . "/{$username}.yaml";

        if (file_exists($file)) {
            return $response->withJson(Response::ResourceExists(), 403);
        }

        $data = [];
        $data['password'] = $parsedBody['password'];
        $data['fullname'] = isset($parsedBody['fullname']) ? $parsedBody['fullname'] : $inflector->titleize($username);
        $data['title'] = isset($parsedBody['title']) ? $parsedBody['title'] : 'User';
        $data['state'] = 'enabled';
        $data['access'] = !empty($parsedBody['access']) ? $parsedBody['access'] : ['site' => ['login' => true]];

        // Create user object and save it
        $user = new User($data);
        $file = CompiledYamlFile::instance($this->grav['locator']->findResource('user://accounts/' . $username . YAML_EXT,
            true, true));
        $user->file($file);
        $user->save();
        $user = User::load($username);

        $resource = new UserResource(array_merge(
            array('username' => $username),
            $user
        ));

        $filter = null;

        if ( !empty($this->config->user->fields) ) {
            $filter = $this->config->user->fields;
        }

        $data = $resource->toJson($filter);

        return $response->withJson($data);
    }

    public function updateUser($user, $new)
    {
        $user = (array) $user;

        foreach ($new as $key => $value) {

            // if a value is null, we remove it from the user
            if ($value === null) {
                unset($user[$key]);
                continue;
            }

            // ignore any hashed password changes
            if ($key === 'hashed_password') {
                continue;
            }

            // handle associative array user info:
            // recursively iterate through child arrays,
            // and update nested properties
            if (array_key_exists($key, $user) && is_array($value)) {
                $user[$key] = $this->updateUser($user[$key], $value);
                continue;
            }

            // create new entry, as key doesn't exist
            // or value is a single field
            $user[$key] = $value;
        }

        return $user;
    }
}
