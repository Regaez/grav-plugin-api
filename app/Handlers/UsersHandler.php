<?php
namespace GravApi\Handlers;

use GravApi\Resources\UserResource;
use GravApi\Responses\Response;
use GravApi\Helpers\ArrayHelper;
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

        if ( empty($parsedBody['email']) ) {
            return $response->withJson(Response::BadRequest('You must provide a `email` field!'), 400);
        }

        $inflector = new Inflector();
        // formats username to be all lowercase, with underscores instead of spaces
        $username = $inflector->underscorize($parsedBody['username']);

        $user = User::load($username);

        if ( $user->exists() ) {
            return $response->withJson(Response::ResourceExists(), 403);
        }

        $data = [];

        // password should always exist
        $data['password'] = $parsedBody['password'];

        $data['email'] = isset($parsedBody['email'])
            ? $parsedBody['email']
            : '';

        // if no fullname is set, fall back to username
        $data['fullname'] = isset($parsedBody['fullname'])
            ? $parsedBody['fullname']
            : $inflector->titleize($username);

        $data['title'] = isset($parsedBody['title'])
            ? $parsedBody['title']
            : 'User';

        // by default, we want to enable new users
        $data['state'] = isset($parsedBody['state'])
            ? $parsedBody['state']
            : 'enabled';

        // by default, the user should at least be able to log in to the site
        $data['access'] = !empty($parsedBody['access'])
            ? $parsedBody['access']
            : ['site' => ['login' => true]];

        $user = $this->createUser($username, $data);

        $data = $this->getFilteredResource($username, $user);

        return $response->withJson($data);
    }

    public function updateUser($request, $response, $args)
    {
        $parsedBody = $request->getParsedBody();
        $username = $args['user'];

        $user = User::load($username);

        if ( !$user->exists() ) {
            return $response->withJson(Response::NotFound(), 404);
        }

        // merge the existing user with the new settings
        $updatedUser = ArrayHelper::merge($user->toArray(), $parsedBody);

        $user = $this->createUser($username, $updatedUser);

        $data = $this->getFilteredResource($username, $user);

        return $response->withJson($data);
    }

    public function deleteUser($request, $response, $args)
    {
        $username = $args['user'];

        $user = User::load($username);

        if ( !$user->exists() ) {
            return $response->withJson(Response::NotFound(), 404);
        }

        $user->file()->delete();

        return $response->withStatus(204);
    }

    // Create user object and save it
    protected function createUser($username, $data)
    {
        $user = new User($data);
        $file = CompiledYamlFile::instance($this->grav['locator']->findResource('user://accounts/' . $username . YAML_EXT,
            true, true));
        $user->file($file);
        $user->save();

        return $user;
    }

    // Create a new User resource and return filtered data
    protected function getFilteredResource($username, $user)
    {
        $resource = new UserResource(array_merge(
            $user->toArray(),
            array('username' => $username)
        ));

        $filter = null;

        if ( !empty($this->config->user->fields) ) {
            $filter = $this->config->user->fields;
        }

        return $resource->toJson($filter);
    }
}
