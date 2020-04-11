<?php
namespace GravApi;

use Monolog\Logger;
use GravApi\Config\Config;
use GravApi\Middlewares\AuthMiddleware;
use GravApi\Handlers\ConfigHandler;
use GravApi\Handlers\NotFoundHandler;
use GravApi\Handlers\PagesHandler;
use GravApi\Handlers\PluginsHandler;
use GravApi\Handlers\UsersHandler;
use GravApi\Config\Constants;

/**
 * Class Api
 * @package GravApi
 */
class Api
{
    // Our Slim app instance
    protected $app;

    /**
     * @var Config
     */
    protected $config;

    public function __construct()
    {
        $this->config = Config::instance();

        // Initialise Slim
        $slimConfig = [
            'settings' => [
                'displayErrorDetails' => true,
                'logger' => [
                    'name' => 'grav-api',
                    'level' => Logger::DEBUG,
                    'path' => __DIR__ . '/../logs/app.log',
                ],
            ]
        ];
        $this->app = new \Slim\App($slimConfig);

        // Override default slim notFoundHandler
        $container = $this->app->getContainer();
        $container['notFoundHandler'] = function ($container) {
            return new NotFoundHandler();
        };

        $this->attachHandlers();
    }

    protected function attachHandlers()
    {
        // We must serve from the base route
        $this->app->group("/{$this->config->route}", function () {

            $this->get('', function ($request, $response, $args) {
                $config = Config::instance();
                $endpoints = $config->getEnabledResourceEndpoints();
                return $response->withJson($endpoints);
            });

            $config = Config::instance();

            $this->group(
                Constants::ENDPOINT_PAGE,
                function () use ($config) {

                    if ($config->pages->get->enabled) {
                        $this->get('', PagesHandler::class . ':getPages')
                        ->add(
                            new AuthMiddleware(
                                $config->pages->get,
                                [Constants::ROLE_PAGES_READ]
                            )
                        );

                        $this->get('/{page:.*}', PagesHandler::class . ':getPage')
                        ->add(
                            new AuthMiddleware($config->pages->get, [
                                Constants::ROLE_PAGES_READ,
                                Constants::ROLE_PAGES_ADVANCED
                            ])
                        );
                    }

                    // TODO: add more fine-grained enabling/disabling support for sub-endpoints to avoid confusion
                    if ($config->pages->get->enabled) {
                        $this->post('/searches', PagesHandler::class . ':findPages')
                        ->add(
                            new AuthMiddleware(
                                $config->pages->get,
                                [Constants::ROLE_PAGES_READ]
                            )
                        );
                    }

                    if ($config->pages->post->enabled) {
                        $this->post('', PagesHandler::class . ':newPage')
                        ->add(
                            new AuthMiddleware(
                                $config->pages->post,
                                [Constants::ROLE_PAGES_CREATE]
                            )
                        );
                    }

                    if ($config->pages->delete->enabled) {
                        $this->delete('/{page:.*}', PagesHandler::class . ':deletePage')
                        ->add(
                            new AuthMiddleware(
                                $config->pages->delete,
                                [Constants::ROLE_PAGES_DELETE]
                            )
                        );
                    }

                    if ($config->pages->patch->enabled) {
                        $this->patch('/{page:.*}', PagesHandler::class . ':updatePage')
                        ->add(
                            new AuthMiddleware(
                                $config->pages->patch,
                                [Constants::ROLE_PAGES_EDIT]
                            )
                        );
                    }
                }
            );

            $this->group(
                Constants::ENDPOINT_USER,
                function () use ($config) {

                    if ($config->users->get->enabled) {
                        $this->get('', UsersHandler::class . ':getUsers')
                        ->add(
                            new AuthMiddleware(
                                $config->users->get,
                                [Constants::ROLE_USERS_READ]
                            )
                        );

                        $this->get('/{user}', UsersHandler::class . ':getUser')
                        ->add(
                            new AuthMiddleware(
                                $config->users->get,
                                [Constants::ROLE_USERS_READ]
                            )
                        );
                    }

                    if ($config->users->post->enabled) {
                        $this->post('', UsersHandler::class . ':newUser')
                        ->add(
                            new AuthMiddleware(
                                $config->users->post,
                                [Constants::ROLE_USERS_CREATE]
                            )
                        );
                    }

                    if ($config->users->delete->enabled) {
                        $this->delete('/{user}', UsersHandler::class . ':deleteUser')
                        ->add(
                            new AuthMiddleware(
                                $config->users->delete,
                                [Constants::ROLE_USERS_DELETE]
                            )
                        );
                    }

                    if ($config->users->patch->enabled) {
                        $this->patch('/{user}', UsersHandler::class . ':updateUser')
                        ->add(
                            new AuthMiddleware(
                                $config->users->patch,
                                [Constants::ROLE_USERS_EDIT]
                            )
                        );
                    }
                }
            );

            $this->group(
                Constants::ENDPOINT_PLUGIN,
                function () use ($config) {
                    if ($config->plugins->get->enabled) {
                        $this->get('', PluginsHandler::class . ':getPlugins')
                        ->add(
                            new AuthMiddleware(
                                $config->plugins->get,
                                [Constants::ROLE_PLUGINS_READ]
                            )
                        );
                    }

                    if ($config->plugins->get->enabled) {
                        $this->get('/{plugin}', PluginsHandler::class . ':getPlugin')
                        ->add(
                            new AuthMiddleware(
                                $config->plugins->get,
                                [Constants::ROLE_PLUGINS_READ]
                            )
                        );
                    }

                    if ($config->plugins->patch->enabled) {
                        $this->patch('/{plugin}', PluginsHandler::class . ':updatePlugin')
                        ->add(
                            new AuthMiddleware(
                                $config->plugins->patch,
                                [Constants::ROLE_PLUGINS_EDIT]
                            )
                        );
                    }
                }
            );

            $this->group(
                Constants::ENDPOINT_CONFIG,
                function () use ($config) {

                    if ($config->configs->get->enabled) {
                        $this->get('', ConfigHandler::class . ':getConfigs')
                        ->add(
                            new AuthMiddleware(
                                $config->configs->get,
                                [Constants::ROLE_CONFIGS_READ]
                            )
                        );
                    }

                    if ($config->configs->get->enabled) {
                        $this->get('/{config}', ConfigHandler::class . ':getConfig')
                        ->add(
                            new AuthMiddleware(
                                $config->configs->get,
                                [Constants::ROLE_CONFIGS_READ]
                            )
                        );
                    }
                }
            );
        });
    }

    public function run()
    {
        $this->app->run();
    }
}
