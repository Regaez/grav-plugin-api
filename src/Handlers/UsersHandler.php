<?php
namespace GravApi\Handlers;

use GravApi\Resources\UserResource;
use GravApi\Resources\UserCollectionResource;
use GravApi\Responses\Response;
use GravApi\Helpers\ArrayHelper;
use Grav\Common\User\Authentication;
use Grav\Common\Inflector;
use Grav\Common\File\CompiledYamlFile;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class PagesHandler
 * @package GravApi\Handlers
 */
class UsersHandler extends BaseHandler
{
    public function getUsers($request, $response, $args)
    {
        $users = [];

        $files = (array) glob($this->grav['locator']->findResource("account://") . '/*' . YAML_EXT);

        if (!$files) {
            return $response->withJson(Response::notFound(), 404);
        }

        foreach ($files as $file) {
            $username = basename($file, YAML_EXT);
            $users[] = $this->grav['accounts']->load($username);
        }

        $resource = new UserCollectionResource($users);

        $this->grav->fireEvent(Constants::EVENT_ON_API_USER_GET_ALL, new Event(['users' => $users]));

        return $response->withJson($resource->toJson());
    }

    public function getUser($request, $response, $args)
    {
        $user = $this->grav['accounts']->load($args['user']);

        if (!$user->exists()) {
            return $response->withJson(Response::notFound(), 404);
        }

        $resource = new UserResource($user);

        $this->grav->fireEvent(Constants::EVENT_ON_API_USER_GET, new Event(['user' => $user]));

        return $response->withJson($resource->toJson());
    }

    public function newUser($request, $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        if (empty($parsedBody['username'])) {
            return $response->withJson(Response::badRequest('You must provide a `username` field!'), 400);
        }

        if (empty($parsedBody['password'])) {
            return $response->withJson(Response::badRequest('You must provide a `password` field!'), 400);
        }

        if (empty($parsedBody['email'])) {
            return $response->withJson(Response::badRequest('You must provide a `email` field!'), 400);
        }

        $inflector = new Inflector();
        // formats username to be all lowercase, with underscores instead of spaces
        $username = $inflector->underscorize($parsedBody['username']);

        $user = $this->grav['accounts']->load($username);

        if ($user->exists()) {
            return $response->withJson(Response::resourceExists(), 403);
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

        $data['groups'] = isset($parsedBody['groups'])
            ? $parsedBody['groups']
            : [];

        $user = $this->createUser($username, $data);

        $resource = new UserResource($user);

        $this->grav->fireEvent(Constants::EVENT_ON_API_USER_CREATE, new Event(['user' => $user]));

        return $response->withJson($resource->toJson());
    }

    public function updateUser($request, $response, $args)
    {
        $parsedBody = $request->getParsedBody();
        $username = $args['user'];

        $user = $this->grav['accounts']->load($username);

        if (!$user->exists()) {
            return $response->withJson(Response::notFound(), 404);
        }

        // handle updating a user password
        if (isset($parsedBody['new_password'])) {
            if (!isset($parsedBody['password'])) {
                return $response->withJson(
                    Response::badRequest('You must provide the existing `password` in order to set a new one!'),
                    400
                );
            }

            // check existing credentials are valid
            if ($user->authenticate($parsedBody['password'])) {
                // creates a hashed version of the password
                $parsedBody['hashed_password'] = Authentication::create($parsedBody['new_password']);
            } else {
                return $response->withJson(
                    Response::badRequest('The existing `password` was invalid! Unable to set a new password.'),
                    400
                );
            }

            // we unset this in favour of the hashed one
            // and always unset so a plain password isn't stored
            unset($parsedBody['new_password']);
        }

        // we never want to store a plain `password` in the user,
        // so we always unset it
        if (isset($parsedBody['password'])) {
            unset($parsedBody['password']);
        }

        // merge the existing user with the new settings
        $updatedUser = ArrayHelper::merge($user->toArray(), $parsedBody);

        $user = $this->createUser($username, $updatedUser);

        $resource = new UserResource($user);

        $this->grav->fireEvent(Constants::EVENT_ON_API_USER_UPDATE, new Event(['user' => $user]));

        return $response->withJson($resource->toJson());
    }

    public function deleteUser($request, $response, $args)
    {
        $username = $args['user'];

        $user = $this->grav['accounts']->load($username);

        if (!$user->exists()) {
            return $response->withJson(Response::notFound(), 404);
        }

        $user->file()->delete();

        $this->grav->fireEvent(Constants::EVENT_ON_API_USER_DELETE, new Event(['user' => $user]));

        return $response->withStatus(204);
    }

    // Create user object and save it
    protected function createUser($username, $data)
    {
        $user = $this->grav['accounts']->load($username);
        $user->update($data);
        $file = CompiledYamlFile::instance($this->grav['locator']->findResource(
            'user://accounts/' . $username . YAML_EXT,
            true,
            true
        ));
        $user->file($file);
        $user->save();

        return $user;
    }
}
