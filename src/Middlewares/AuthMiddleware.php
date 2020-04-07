<?php
namespace GravApi\Middlewares;

use Grav\Common\Grav;
use GravApi\Config\Constants;
use GravApi\Config\Method;
use GravApi\Responses\Response;

/**
 * Class AuthMiddleware
 * @package GravApi\Middlewares
 */
class AuthMiddleware
{
    /**
     * @var Method
     */
    protected $config;

    /**
     * @var string[]
     */
    protected $roles;

    /**
     * @param Method $config
     * @param string[] $roles array of Constants::ROLES_* strings
     */
    public function __construct(Method $config, array $roles = array())
    {
        $this->config = $config;
        $this->grav = Grav::instance();

        // These are default roles which allow for a user
        // to use any part of the API
        $defaultRoles = ['admin.super', Constants::ROLE_SUPER];

        $this->roles = array_merge($defaultRoles, $roles);
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
        if ($this->config->useAuth) {
            // We try to get the user from the session
            $sessionUser = $this->grav['session']->user;

            if ($sessionUser) {
                // Check if the session user has the required roles
                if (!$this->checkRoles($sessionUser)) {
                    return $response->withJson(Response::unauthorized(), 401);
                }
            } else {
                // Otherwise we check credentials from Basic auth
                $authUser = implode(' ', $request->getHeader('PHP_AUTH_USER')) ?: '';
                $authPass = implode(' ', $request->getHeader('PHP_AUTH_PW')) ?: '';

                if (!$this->isAuthorised($authUser, $authPass)) {
                    return $response->withJson(Response::unauthorized(), 401);
                }
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
        $user = $this->grav['accounts']->load($username);
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
