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

                $urls = [
                    Constants::TYPE_PAGE => $config->getEndpoint(
                        Constants::TYPE_PAGE
                    ),
                    Constants::TYPE_USER => $config->getEndpoint(
                        Constants::TYPE_USER
                    ),
                    Constants::TYPE_PLUGIN => $config->getEndpoint(
                        Constants::TYPE_PLUGIN
                    ),
                    Constants::TYPE_CONFIG => $config->getEndpoint(
                        Constants::TYPE_CONFIG
                    )
                ];

                return $response->withJson($urls);
            });

            $config = Config::instance();

            $this->group(
                Constants::ENDPOINT_PAGE,
                function () use ($config) {

                    if ($config->pages->get->enabled) {
                        $this->get('', PagesHandler::class . ':getPages')
                            ->add(new AuthMiddleware($config->pages->get));

                        $this->get('/{page:.*}', PagesHandler::class . ':getPage')
                            ->add(new AuthMiddleware($config->pages->get));
                    }

                    if ($config->pages->post->enabled) {
                        $this->post('', PagesHandler::class . ':newPage')
                            ->add(new AuthMiddleware($config->pages->post));
                    }

                    if ($config->pages->delete->enabled) {
                        $this->delete('/{page:.*}', PagesHandler::class . ':deletePage')
                            ->add(new AuthMiddleware($config->pages->delete));
                    }

                    if ($config->pages->patch->enabled) {
                        $this->patch('/{page:.*}', PagesHandler::class . ':updatePage')
                            ->add(new AuthMiddleware($config->pages->patch));
                    }
                }
            );

            $this->group(
                Constants::ENDPOINT_USER,
                function () use ($config) {

                    if ($config->users->get->enabled) {
                        $this->get('', UsersHandler::class . ':getUsers')
                            ->add(new AuthMiddleware($config->users->get));

                        $this->get('/{user}', UsersHandler::class . ':getUser')
                            ->add(new AuthMiddleware($config->users->get));
                    }

                    if ($config->users->post->enabled) {
                        $this->post('', UsersHandler::class . ':newUser')
                            ->add(new AuthMiddleware($config->users->post));
                    }

                    if ($config->users->delete->enabled) {
                        $this->delete('/{user}', UsersHandler::class . ':deleteUser')
                        ->add(new AuthMiddleware($config->users->delete));
                    }

                    if ($config->users->patch->enabled) {
                        $this->patch('/{user}', UsersHandler::class . ':updateUser')
                            ->add(new AuthMiddleware($config->users->patch));
                    }
                }
            );

            $this->group(
                Constants::ENDPOINT_PLUGIN,
                function () use ($config) {
                    if ($config->plugins->get->enabled) {
                        $this->get('', PluginsHandler::class . ':getPlugins')
                            ->add(new AuthMiddleware($config->plugins->get));
                    }

                    if ($config->plugins->get->enabled) {
                        $this->get('/{plugin}', PluginsHandler::class . ':getPlugin')
                            ->add(new AuthMiddleware($config->plugins->get));
                    }
                }
            );

            $this->group(
                Constants::ENDPOINT_CONFIG,
                function () use ($config) {

                    if ($config->configs->get->enabled) {
                        $this->get('', ConfigHandler::class . ':getConfigs')
                            ->add(new AuthMiddleware($config->configs->get));
                    }

                    if ($config->configs->get->enabled) {
                        $this->get('/{config}', ConfigHandler::class . ':getConfig')
                            ->add(new AuthMiddleware($config->configs->get));
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
