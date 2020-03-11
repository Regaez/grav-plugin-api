<?php
namespace GravApi\Config;

/**
 * Class Constants
 * @package GravApi\Config
 */
class Constants
{
    const DEFAULT_ROUTE = 'api';

    // Resource types
    const TYPE_PAGE = 'page';
    const TYPE_USER = 'user';
    const TYPE_PLUGIN = 'plugin';
    const TYPE_CONFIG = 'config';

    // All available types
    const TYPES = [
        Constants::TYPE_PAGE,
        Constants::TYPE_USER,
        Constants::TYPE_PLUGIN,
        Constants::TYPE_CONFIG
    ];

    // Endpoints
    const ENDPOINT_PAGE = '/pages';
    const ENDPOINT_USER = '/users';
    const ENDPOINT_PLUGIN = '/plugins';
    const ENDPOINT_CONFIG = '/configs';

    // All available endpoints
    const ENDPOINTS = [
        Constants::ENDPOINT_PAGE,
        Constants::ENDPOINT_USER,
        Constants::ENDPOINT_PLUGIN,
        Constants::ENDPOINT_CONFIG
    ];

    // Methods
    const METHOD_GET = 'get';
    const METHOD_PATCH = 'patch';
    const METHOD_POST = 'post';
    const METHOD_DELETE = 'delete';

    // All available methods
    const METHODS = [
        Constants::METHOD_GET,
        Constants::METHOD_PATCH,
        Constants::METHOD_POST,
        Constants::METHOD_DELETE
    ];
}
