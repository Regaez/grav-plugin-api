<?php
namespace GravApi;

use Monolog\Logger;
use GravApi\Config\Config;
use GravApi\Middlewares\AuthMiddleware;
use GravApi\Handlers\ConfigHandler;
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
    protected $settings;

    public function __construct()
    {
        $this->settings = Config::instance();

        // Initialise Slim
        $config = [
            'settings' => [
                'displayErrorDetails' => true,
                'logger' => [
                    'name' => 'slim-app',
                    'level' => Logger::DEBUG,
                    'path' => __DIR__ . '/../logs/app.log',
                ],
            ]
        ];
        $this->app = new \Slim\App($config);

        $this->attachHandlers();
    }

    protected function attachHandlers()
    {
        // We must serve from the base route
        $this->app->group("/{$this->settings->route}", function () {

            $this->get('', function ($request, $response, $args) {
                $settings = Config::instance();

                $urls = [
                    Constants::TYPE_PAGE => $settings->getEndpoint(
                        Constants::TYPE_PAGE
                    ),
                    Constants::TYPE_USER => $settings->getEndpoint(
                        Constants::TYPE_USER
                    ),
                    Constants::TYPE_PLUGIN => $settings->getEndpoint(
                        Constants::TYPE_PLUGIN
                    ),
                    Constants::TYPE_CONFIG => $settings->getEndpoint(
                        Constants::TYPE_CONFIG
                    )
                ];

                return $response->withJson($urls);
            });

            $settings = Config::instance();

            $this->group(
                Constants::ENDPOINT_PAGE,
                function () use ($settings) {

                    if ($settings->pages->get->enabled) {
                        $this->get('', PagesHandler::class . ':getPages')
                            ->add(new AuthMiddleware($settings->pages->get));

                        $this->get('/{page:.*}', PagesHandler::class . ':getPage')
                            ->add(new AuthMiddleware($settings->pages->get));
                    }

                    if ($settings->pages->post->enabled) {
                        $this->post('', PagesHandler::class . ':newPage')
                            ->add(new AuthMiddleware($settings->pages->post));
                    }

                    if ($settings->pages->delete->enabled) {
                        $this->delete('/{page:.*}', PagesHandler::class . ':deletePage')
                            ->add(new AuthMiddleware($settings->pages->delete));
                    }

                    if ($settings->pages->patch->enabled) {
                        $this->patch('/{page:.*}', PagesHandler::class . ':updatePage')
                            ->add(new AuthMiddleware($settings->pages->patch));
                    }
                }
            );

            $this->group(
                Constants::ENDPOINT_USER,
                function () use ($settings) {

                    if ($settings->users->get->enabled) {
                        $this->get('', UsersHandler::class . ':getUsers')
                            ->add(new AuthMiddleware($settings->users->get));

                        $this->get('/{user}', UsersHandler::class . ':getUser')
                            ->add(new AuthMiddleware($settings->users->get));
                    }

                    if ($settings->users->post->enabled) {
                        $this->post('', UsersHandler::class . ':newUser')
                            ->add(new AuthMiddleware($settings->users->post));
                    }

                    if ($settings->users->delete->enabled) {
                        $this->delete('/{user}', UsersHandler::class . ':deleteUser')
                        ->add(new AuthMiddleware($settings->users->delete));
                    }

                    if ($settings->users->patch->enabled) {
                        $this->patch('/{user}', UsersHandler::class . ':updateUser')
                            ->add(new AuthMiddleware($settings->users->patch));
                    }
                }
            );

            $this->group(
                Constants::ENDPOINT_PLUGIN,
                function () use ($settings) {
                    if ($settings->plugins->get->enabled) {
                        $this->get('', PluginsHandler::class . ':getPlugins')
                            ->add(new AuthMiddleware($settings->plugins->get));
                    }

                    if ($settings->plugins->get->enabled) {
                        $this->get('/{plugin}', PluginsHandler::class . ':getPlugin')
                            ->add(new AuthMiddleware($settings->plugins->get));
                    }
                }
            );

            $this->group(
                Constants::ENDPOINT_CONFIG,
                function () use ($settings) {

                    if ($settings->configs->get->enabled) {
                        $this->get('', ConfigHandler::class . ':getConfigs')
                            ->add(new AuthMiddleware($settings->configs->get));
                    }

                    if ($settings->configs->get->enabled) {
                        $this->get('/{config}', ConfigHandler::class . ':getConfig')
                            ->add(new AuthMiddleware($settings->configs->get));
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
