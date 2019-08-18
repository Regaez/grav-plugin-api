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

    // Endpoints
    const ENDPOINT_PAGE = '/pages';
    const ENDPOINT_USER = '/users';
    const ENDPOINT_PLUGIN = '/plugins';
    const ENDPOINT_CONFIG = '/configs';

    // Methods
    const METHOD_GET = 'get';
    const METHOD_PATCH = 'patch';
    const METHOD_POST = 'post';
    const METHOD_DELETE = 'delete';

    /**
     * All available methods
     */
    const METHODS = [
        METHOD_GET,
        METHOD_PATCH,
        METHOD_POST,
        METHOD_DELETE
    ];
}
