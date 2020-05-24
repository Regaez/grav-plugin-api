# Authentication

- [Requirements](#requirements)
- [Authorisation Methods](#authorisation-methods)
  - [Basic Auth](#basic-auth)
    - [Generating a Basic token](#generating-a-basic-token)
    - [PHP CGI and PHP FastCGI](#php-cgi-and-php-fastcgi)
  - [Session User](#session-user)
- [Roles and Permissions](#roles-and-permissions)
  - [Custom API Roles](#custom-api-roles)
    - [Pages](#pages)
    - [Users](#users)
    - [Plugins](#plugins)
    - [Configs](#configs)
  - [Advanced Access](#advanced-access)
    - [Routes](#routes)
    - [Taxonomy](#taxonomy)
    - [Advanced Access full example](#advanced-access-full-example)
  - [Editing in the Admin UI](#editing-in-the-admin-ui)
  - [Group Permissions](#group-permissions)

## Requirements

In order to authenticate with the API, you **must** have valid user account credentials. If you are unsure of how to create a new user, [please read the official Grav documentation.](https://learn.getgrav.org)

The user account **must** have `status: enabled`, i.e. they can log into the site.

> **NOTE:** The following information only applies when a resource's endpoint is _configured to use authentication_. Please refer to [docs/CONFIGURATION.md](https://github.com/Regaez/grav-plugin-api/blob/master/docs/CONFIGURATION.md) to learn more.

## Authorisation Methods

There are currently two methods of authorisation supported by the API plugin: basic auth, and session user.

There is an open issue to [support login via OAuth](https://github.com/Regaez/grav-plugin-api/issues/68), but it has not yet been implemented. Please comment, or track the issue, if you are interested in this feature.

### Basic Auth

> **IMPORTANT NOTE:** If using Basic auth, you **must use HTTPS** otherwise your request headers can be read, the auth token decoded, and your site credentials leaked!

You can authorise your external API requests by passing the `Authorization` header with a `Basic` token:

```
Authorization: Basic ZGV2ZWxvcG1lbnQ6RDN2ZWxvcG1lbnQ=
```

#### Generating a Basic token

A `Basic` token is simply your `username` and `password` concatenated with a colon, `:`, and encoded in Base64.

You can generate this quite easily in the browser console using javascript:

```js
btoa('username:password')
```

Or in Mac OS X terminal:

```bash
echo "username:password" | base64
```

#### PHP CGI and PHP FastCGI

If you are using PHP CGI or PHP FastCGI, then the `Authorization` header is not automatically attached to the request when forwarded to Grav. This means you are unable to use Basic auth unless you make some changes to your site's `.htaccess` file.

Adding the following `RewriteRule` should enable authorisation:

```
RewriteEngine on
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
```

### Session User

If using the API from within the context of your Grav site (i.e. you are making AJAX requests from your theme), you can use the _current session user_ to authorise the requests.

The user must be logged into the Grav site and have an active session for this authorisation method to work. If their session expires, then so too will their access to the API.

> **NOTE:** This user **must** have the required roles/permissions in order to interact with the requested resources.

## Roles and Permissions

There are a number of custom roles which have been added in order to provide fine-grained access control to manage site data via the API.

> **NOTE:** These permissions _only_ apply to requests made to the API. They will not affect normal site access or the Admin plugin pages.

### Custom API Roles

- `api.super` - This role will permit a user to call _any_ endpoint/method of the API. You should be very wary of giving this role to any user account!

#### Pages

- `api.pages_read` - Allows a user to **read** _any_ page.
- `api.pages_edit` - Allows a user to **edit** _any_ page.
- `api.pages_create` - Allows a user to **create** _any_ page.
- `api.pages_delete` - Allows a user to **delete** _any_ page.
- `api.pages_advanced` - Enables _advanced access_ permissions for the user. Please see the [Advanced Access](#advanced-access) section below.

#### Users

- `api.users_read` - Allows a user to **read** _any_ user's data.
- `api.users_edit` - Allows a user to **edit** _any_ user's data.
- `api.users_create` - Allows a user to **create** _any_ user's data.
- `api.users_delete` - Allows a user to **delete** _any_ user's data.

#### Plugins

- `api.plugins_read` - Allows a user to **read** _any_ plugin's config.
- `api.plugins_edit` - Allows a user to **edit** _any_ plugin's config.
- `api.plugins_install` - Allows a user to **install** a new plugin.
- `api.plugins_uninstall` - Allows a user to **uninstall** a plugin.

#### Configs

- `api.configs_read` - Allows a user to **read** _any_ config file.
- `api.configs_edit` - Allows a user to **edit** _any_ config file.

> **NOTE:** We never allow access to the site's `security.yaml` file.

### Advanced Access

You may want to restrict a user's to a limited number of pages on your site. This is where _advanced access_ comes into play. For each HTTP method supported by the API, e.g. `get`, `post`, `patch`, `delete`, you can configure exactly which page `routes` and `taxonomy` values a user (or group) is allowed to access.

In order to enable this feature, you must give a user the `api.pages_advanced_access` role.

Within the user account file, `user/accounts/*.yaml` (where `*` represents a username), you must specify a nested object structure, such as:

```yaml
api:
  advanced_access:
    pages:
```

Within pages, you can define four properties, which match the HTTP method names.

- `get` - configure the user's **read** permissions.
- `patch` - configure the user's **edit** permissions.
- `post` - configure the user's **create** permissions.
- `delete` - configure the user's **delete** permissions.

#### Routes

For all supported HTTP methods, you can specify a `routes` array, where you can list all of the pages available to the user.

The routes **must** start with a slash, assuming the site root, and **must not** contain the site domain name. For example: `/blog`

```yaml
api:
  advanced_access:
    pages:
      get:
        routes:
          - /home
          - /blog
          - /contact
```

There is also a special _any descendants_ wildcard, which will allow access to all subpages within a route.

For example, `/blog/*` will allow a user to read the page `/blog/child`, whereas a route `/blog` will _not_ allow the user to read the `/blog/child`.

However, it is important to note that `/blog/*` will _not_ allow access to `/blog` itself. You must specify both in the routes array, or use a taxonomy value.

```yaml
api:
  advanced_access:
    pages:
      get:
        routes:
          - /blog   # Allows access to this single page
          - /blog/* # Allows access to any descendant, but NOT /blog itself
```

#### Taxonomy

For `get`, `patch`, and `delete` methods, you can specify any amount of taxonomy values. If _any one of those values_ are present on the requested page resource, then access will be allowed.

> **NOTE:** You cannot specify `taxonomy` values for the `post` method, only `routes`. If you do, they will be ignored.

Much the same as adding `routes`, you can add any number of taxonomy fields/values to a user's account file. The `taxonomy` object should be structure the same as you would for any page's frontmatter. If you are unsure of how to construct taxonomy properties, please consult the [official Grav documentation](https://learn.getgrav.org).

```yaml
api:
  advanced_access:
    pages:
      get:
        taxonomy:
          category:
            - blog
          tag:
            - grav
```

#### Advanced Access full example

See below for an example account file:

```yaml
# accounts/joe.yaml

email: joe@email.com
fullname: Joe Bloggs
title: Staff Member
state: enabled
access:
  api:
    pages_advanced_access: true
api:
  advanced_access:
    pages:
      get:
        routes:
          - /staff-blog   # Can only read this page
          - /staff-blog/* # Can read any descendants of this page
        taxonomy:
          category:
            - staff-blog  # Instead of /staff-blog/*, you could use a taxonomy
            - staff       # Can read any pages with this taxonomy value
          tag:
            - staff-news  # Can read any pages with this taxonomy value
      post:
        routes:
          - /staff/joe   # Can only create this page
          - /staff-blog/* # Can create descendants of /staff-blog
      patch:
        routes:
          - /staff/joe   # Can only edit this page
          - /staff/joe/* # Can edit any descendants of /staff/joe
        taxonomy:
          category:
            - staff-blog  # Can edit any page with this taxonomy value
      delete:
        routes:
          - /staff/joe   # Can only delete this page
          - /staff-blog/* # Can delete any descendants of /staff-blog
        taxonomy:
          category:
            - staff-blog  # Can delete any page with this taxonomy value

```

### Editing in the Admin UI

The API plugin will register all the custom roles with the `admin` plugin on initialisation. This means you are also able to edit them on the user's profile page if you possess the `admin.super` role.

For ease of customisation, the `user/account` blueprint has been extended to include a form to manage user's custom roles and [Advanced Access](#advanced-access) rights.

### Group Permissions

Any of the custom API roles can be inherited from a user's group, allowing for flexible permission management to many users at once. This also applies to the special [Advanced Access](#advanced-access) permissions.

The setup is exactly the same as for the user account. In your `user/config/groups.yaml` file, define the access roles as you normally would for a user.

For _Advanced Access_ you can add the same `api.advanced_access` structure, but it must be within a specific group's properties, e.g.

```yaml
# user/config/groups.yaml

authors:
  readableName: Authors
  description: A group of blog authors
  access:
    api:
      pages_advanced_access: true
    admin:
      login: true
    site:
      login: true
  api:
    advanced_access:
      pages:
        get:
          taxonomy:
            category:
              - blog
        patch:
          taxonomy:
            category:
              - blog
        post:
          routes:
            - /blog/*
        delete:
          routes:
            - /blog/*
```
The above config would allow any user who is part of the `authors` group to:

- **read** any pages with `category: blog` taxonomy
- **edit** any pages with `category: blog` taxonomy
- **create** any pages that are children/descendants of `/blog`
- **delete** any pages that are children/descendants of `/blog`
