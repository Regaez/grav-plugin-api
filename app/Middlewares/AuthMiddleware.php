<?php
namespace GravApi\Middlewares;

use Grav\Common\User\User;
use GravApi\Config\Config;
use GravApi\Responses\Response;

/**
 * Class AuthMiddleware
 * @package GravApi\Middlewares
 */
class AuthMiddleware
{
    // Our endpoint config
    protected $config;

    protected $roles;

    public function __construct($config) {
        $this->config = $config;

        // These are the default roles required for a user to use the API
        $this->roles = ['admin.api', 'admin.super'];
    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        if ( !empty($this->config->auth) ) {

            $authUser = implode(' ', $request->getHeader('PHP_AUTH_USER')) ?: '';
            $authPass = implode(' ', $request->getHeader('PHP_AUTH_PW')) ?: '';

            if ( !$this->isAuthorised($authUser, $authPass)) {
                return $response->withJson(Response::Unauthorized(), 401);
            }
        }

        $response = $next($request, $response);

        return $response;
    }

    /**
     * Authenticate against specified Grav user
     * @param  string $username
     * @param  string $password
     * @return bool
     */
    public function isAuthorised($username, $password)
    {
        $user = User::load($username);
        $isAuthenticated = $user->authenticate($password);

        if ($isAuthenticated) {
            $user->authenticated = true;

            if ($this->checkRoles($user)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the user has any of the required roles
     * @param  \Grav\Common\User\User $user
     * @return bool
     */
    public function checkRoles($user)
    {
        foreach ($this->roles as $role) {
            if ($user->authorize($role)) {
                return true;
            }
        }
        return false;
    }
}
