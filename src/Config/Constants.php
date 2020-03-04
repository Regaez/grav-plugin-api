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
    const TYPE_TAXONOMY = 'taxonomy';

    // Endpoints
    const ENDPOINT_PAGE = '/pages';
    const ENDPOINT_USER = '/users';
    const ENDPOINT_PLUGIN = '/plugins';
    const ENDPOINT_CONFIG = '/configs';
    const ENDPOINT_TAXONOMY = '/taxonomies';

    // All available endpoints
    const ENDPOINTS = [
        Constants::ENDPOINT_PAGE,
        Constants::ENDPOINT_USER,
        Constants::ENDPOINT_PLUGIN,
        Constants::ENDPOINT_CONFIG,
        Constants::ENDPOINT_TAXONOMY
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
