<?php
namespace GravApi\Middlewares;

use Grav\Common\Grav;
use GravApi\Config\Method;
use GravApi\Helpers\AuthHelper;
use GravApi\Responses\Response;
use Grav\Common\User\Interfaces\UserInterface;
use GravApi\Config\Constants;
use RocketTheme\Toolbox\Event\Event;

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
        $this->roles = $roles;
    }

    /**
     * @param  \Slim\Http\Request $request  PSR7 request
     * @param  \Slim\Http\Response $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @return \Slim\Http\Response
     */
    public function __invoke($request, $response, $next)
    {
        if ($this->config->useAuth) {
            // We try to get the user from the session

            /** @var UserInterface */
            $sessionUser = $this->grav['session']->user;

            if ($sessionUser) {
                // Check if the session user has the required roles
                if (!AuthHelper::checkRoles($sessionUser, $this->roles)) {
                    $this->grav->fireEvent(Constants::EVENT_ON_API_UNAUTHORIZED_REQUEST, new Event(['user' => $sessionUser, 'roles' => $this->roles]));
                    return $response->withJson(Response::unauthorized(), 401);
                }

                // Authorisation has now passed for session auth,
                // so we decorate the request with the user
                $request = $request->withAttribute('user', $sessionUser);
            } else {
                // Otherwise we check credentials from Basic auth
                $authUser = implode(' ', $request->getHeader('PHP_AUTH_USER')) ?: '';
                $authPass = implode(' ', $request->getHeader('PHP_AUTH_PW')) ?: '';

                /** @var UserInterface */
                $user = $this->isAuthorised($authUser, $authPass);

                if (!$user) {
                    $this->grav->fireEvent(Constants::EVENT_ON_API_UNAUTHORIZED_REQUEST, new Event(['user' => $sessionUser, 'roles' => $this->roles]));
                    return $response->withJson(Response::unauthorized(), 401);
                }

                // Authorisation has now passed for Basic auth,
                // so we decorate the request with the user
                $request = $request->withAttribute('user', $user);
            }
        }

        $response = $next($request, $response);

        return $response;
    }

    /**
     * Authenticate against specified Grav user
     * @param  string $username
     * @param  string $password
     * @return bool|User
     */
    public function isAuthorised($username, $password)
    {
        $user = $this->grav['accounts']->load($username);
        $isAuthenticated = $user->authenticate($password);

        if ($isAuthenticated) {
            $user->authenticated = true;

            if (AuthHelper::checkRoles($user, $this->roles)) {
                return $user;
            }
        }

        return false;
    }
}
