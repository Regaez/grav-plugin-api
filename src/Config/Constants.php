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

    // Roles
    const ROLE_SUPER = 'api.super';
    const ROLE_PAGES_READ = 'api.pages_read';
    const ROLE_PAGES_DELETE = 'api.pages_delete';
    const ROLE_PAGES_EDIT = 'api.pages_edit';
    const ROLE_PAGES_CREATE = 'api.pages_create';
    const ROLE_PAGES_ADVANCED = 'api.pages_advanced_access';
    const ROLE_USERS_READ = 'api.users_read';
    const ROLE_USERS_DELETE = 'api.users_delete';
    const ROLE_USERS_CREATE = 'api.users_create';
    const ROLE_USERS_EDIT = 'api.users_edit';
    const ROLE_PLUGINS_READ = 'api.plugins_read';
    const ROLE_PLUGINS_EDIT = 'api.plugins_edit';
    const ROLE_PLUGINS_INSTALL = 'api.plugins_install';
    const ROLE_PLUGINS_UNINSTALL = 'api.plugins_uninstall';
    const ROLE_CONFIGS_READ = 'api.configs_read';
    const ROLE_CONFIGS_EDIT = 'api.configs_edit';

    /**
     * All available API roles
     */
    const ROLES = [
        Constants::ROLE_SUPER,
        Constants::ROLE_PAGES_READ,
        Constants::ROLE_PAGES_DELETE,
        Constants::ROLE_PAGES_EDIT,
        Constants::ROLE_PAGES_CREATE,
        Constants::ROLE_PAGES_ADVANCED,
        Constants::ROLE_USERS_READ,
        Constants::ROLE_USERS_DELETE,
        Constants::ROLE_USERS_CREATE,
        Constants::ROLE_USERS_EDIT,
        Constants::ROLE_PLUGINS_READ,
        Constants::ROLE_PLUGINS_EDIT,
        Constants::ROLE_PLUGINS_INSTALL,
        Constants::ROLE_PLUGINS_UNINSTALL,
        Constants::ROLE_CONFIGS_READ,
        Constants::ROLE_CONFIGS_EDIT
    ];

    /**
     * Pattern for route matching which will allow "any descendents"
     */
    const REGEX_DESCENDENT_WILDCARD = '/\/\*$/';
}
