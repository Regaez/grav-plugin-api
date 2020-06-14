# Api Events

- [Overview](#overview)
- [Resource Type](#resource-type)
  - [Page](#page)
    - [onApiPageGetAll](#onapipagegetall)
    - [onApiPageGet](#onapipageget)
    - [onApiPageFind](#onapipagefind)
    - [onApiPageCreate](#onapipagecreate)
    - [onApiPageUpdate](#onapipageupdate)
    - [onApiPageDelete](#onapipagedelete)
  - [User](#user)
    - [onApiUserGetAll](#onapiusergetall)
    - [onApiUserGet](#onapiuserget)
    - [onApiUserCreate](#onapiusercreate)
    - [onApiUserUpdate](#onapiuserupdate)
    - [onApiUserDelete](#onapiuserdelete)
  - [Config](#config)
    - [onApiConfigGetAll](#onapiconfiggetall)
    - [onApiConfigGet](#onapiconfigget)
    - [onApiConfigUpdate](#onapiconfigupdate)
  - [Plugin](#plugin)
    - [onApiPluginGetAll](#onapiplugingetall)
    - [onApiPluginGet](#onapipluginget)
    - [onApiPluginUpdate](#onapipluginupdate)

## Overview

There are a number of custom events fired by the Api plugin that will allow third party plugins to subscribe to in order to perform some action based on the the requested resource.

If you are not familiar with configuring a Grav plugin/theme to listen to events, then [please refer to the official Grav documentation](https://learn.getgrav.org) to learn how to use these.

## Resource Type

### Page

Whenever a `Page` resource is succesfully requested, there will be a custom event fired by the API plugin including the affected resource as a property of the `Event` itself.

List of `Page` events:

  - [onApiPageGetAll](#onapipagegetall)
  - [onApiPageGet](#onapipageget)
  - [onApiPageFind](#onapipagefind)
  - [onApiPageCreate](#onapipagecreate)
  - [onApiPageUpdate](#onapipageupdate)
  - [onApiPageDelete](#onapipagedelete)

Please refer to the example code provided for full documentation of the available properties for each custom event.

#### onApiPageGetAll

This event is fired any time the `GET /pages` endpoint is successfully requested.

```php
function onApiPageGetAll(Event $e) {
    /**
     * A Grav collection containing all pages returned in the API response.
     *
     * @var \Grav\Common\Page\Collection
     */
    $e['collection'];
}
```

#### onApiPageGet

This event is fired any time the `GET /pages/{id}` endpoint is successfully requested.

```php
function onApiPageGet(Event $e) {
    /**
     * The Grav page returned in the API response.
     *
     * @var \Grav\Common\Page\Interfaces\PageInterface
     */
    $e['page'];
}
```

#### onApiPageFind

This event is fired any time the `POST /pages/search` endpoint is successfully requested.

```php
function onApiPageFind(Event $e) {
    /**
     * The Grav collection containing all pages returned by the API response that
     * matched the filter parameters.
     *
     * @var \Grav\Common\Page\Collection
     */
    $e['collection'];
}
```

#### onApiPageCreate

This event is fired any time the `POST /pages` endpoint is successfully requested.

```php
function onApiPageCreate(Event $e) {
    /**
     * The Grav page created by the API request.
     *
     * @var \Grav\Common\Page\Interfaces\PageInterface
     */
    $e['page'];
}
```

#### onApiPageUpdate

This event is fired any time the `PATCH /pages/{id}` endpoint is successfully requested.

```php
function onApiPageUpdate(Event $e) {
    /**
     * The Grav page updated by the API request.
     *
     * @var \Grav\Common\Page\Interfaces\PageInterface
     */
    $e['page'];
}
```

#### onApiPageDelete

This event is fired any time the `DELETE /pages/{id}` endpoint is successfully requested.

```php
function onApiPageDelete(Event $e) {
    /**
     * The route of the Grav page deleted by the API request.
     *
     * @var string
     */
    $e['route'];
}
```

### User

Whenever a `User` resource is succesfully requested, there will be a custom event fired by the API plugin including the affected resource as a property of the `Event` itself.

List of `User` events:

  - [onApiUserGetAll](#onapiusergetall)
  - [onApiUserGet](#onapiuserget)
  - [onApiUserCreate](#onapiusercreate)
  - [onApiUserUpdate](#onapiuserupdate)
  - [onApiUserDelete](#onapiuserdelete)

Please refer to the example code provided for full documentation of the available properties for each custom event.

#### onApiUserGetAll

This event is fired any time the `GET /users` endpoint is successfully requested.

```php
function onApiUserGetAll(Event $e) {
    /**
     * An array of Grav users returned in the API response.
     *
     * @var Grav\Common\User\User[]
     */
    $e['users'];
}
```

#### onApiUserGet

This event is fired any time the `GET /user/{id}` endpoint is successfully requested.

```php
function onApiUserGet(Event $e) {
    /**
     * The Grav user returned in the API response.
     *
     * @var Grav\Common\User\User
     */
    $e['user'];
}
```

#### onApiUserCreate

This event is fired any time the `POST /users` endpoint is successfully requested.

```php
function onApiUserCreate(Event $e) {
    /**
     * The Grav user created by the API request.
     *
     * @var Grav\Common\User\User
     */
    $e['user'];
}
```

#### onApiUserUpdate

This event is fired any time the `PATCH /users/{id}` endpoint is successfully requested.

```php
function onApiUserUpdate(Event $e) {
    /**
     * The Grav user updated by the API request.
     *
     * @var Grav\Common\User\User
     */
    $e['user'];
}
```

#### onApiUserDelete

This event is fired any time the `DELETE /users/{id}` endpoint is successfully requested.

```php
function onApiUserDelete(Event $e) {
    /**
     * The username of the Grav user deleted by the API request.
     *
     * @var string
     */
    $e['username'];
}
```

### Config

Whenever a `Config` resource is succesfully requested, there will be a custom event fired by the API plugin including the affected resource as a property of the `Event` itself.

List of `Config` events:

  - [onApiConfigGetAll](#onapiconfiggetall)
  - [onApiConfigGet](#onapiconfigget)
  - [onApiConfigUpdate](#onapiconfigupdate)

Please refer to the example code provided for full documentation of the available properties for each custom event.

#### onApiConfigGetAll

This event is fired any time the `GET /configs` endpoint is successfully requested.

```php
function onApiConfigGetAll(Event $e) {
    /**
     * An array of GravApi ConfigModels returned in the API response.
     *
     * @var \GravApi\Models\ConfigModel[]
     */
    $e['configs'];
}
```

#### onApiConfigGet

This event is fired any time the `GET /configs/{id}` endpoint is successfully requested.

```php
function onApiConfigGet(Event $e) {
    /**
     * The GravApi ConfigModel returned in the API response.
     *
     * @var \GravApi\Models\ConfigModel
     */
    $e['config'];
}
```

#### onApiConfigUpdate

This event is fired any time the `PATCH /configs/{id}` endpoint is successfully requested.

```php
function onApiConfigUpdate(Event $e) {
    /**
     * The GravApi ConfigModel returned in the API response.
     *
     * @var \GravApi\Models\ConfigModel
     */
    $e['config'];
}
```

### Plugin

Whenever a `Plugin` resource is succesfully requested, there will be a custom event fired by the API plugin including the affected resource as a property of the `Event` itself.

List of `Plugin` events:

  - [onApiPluginGetAll](#onapiplugingetall)
  - [onApiPluginGet](#onapipluginget)
  - [onApiPluginUpdate](#onapipluginupdate)

Please refer to the example code provided for full documentation of the available properties for each custom event.

#### onApiPluginGetAll

This event is fired any time the `GET /plugins` endpoint is successfully requested.

```php
function onApiPluginGetAll(Event $e) {
    /**
     * An array of Grav Plugins returned in the API response.
     *
     * @var Grav\Common\Plugin[]
     */
    $e['plugins'];
}
```

#### onApiPluginGet

This event is fired any time the `GET /plugins/{id}` endpoint is successfully requested.

```php
function onApiPluginGet(Event $e) {
    /**
     * The Grav Plugin returned in the API response.
     *
     * @var Grav\Common\Plugin
     */
    $e['plugin'];
}
```

#### onApiPluginUpdate

This event is fired any time the `PATCH /plugins/{id}` endpoint is successfully requested.

```php
function onApiPluginUpdate(Event $e) {
    /**
     * The Grav Plugin updated by the API request.
     *
     * @var Grav\Common\Plugin
     */
    $e['plugin'];
}
```
