# Frequently Asked Questions

- [Is this production-ready?](#is-this-production-ready)
- [Which Grav versions are supported?](#which-grav-versions-are-supported)
- [When should I use this plugin?](#when-should-i-use-this-plugin)
- [What endpoints are available?](#what-endpoints-are-available)
- [Why isn't Basic Authentication working for me?](#why-isnt-basic-authentication-working-for-me)
- [How can I make my plugin/theme do something after an API request?](#how-can-i-make-my-plugintheme-do-something-after-an-api-request)
- [Does the Api plugin trigger Git Sync?](#does-the-api-plugin-trigger-git-sync)

## Is this production-ready?

**This plugin is still a work-in-progress.** The plugin is not yet considered to be in a "stable" state. Therefore, we are not concerned with making "breaking changes" to the API specification, nor maintaining a comprehensive changelog, _until version `1.0.0` has been published to GPM_.

> **It is not recommended to use this plugin on a production site. Install it at your own risk.**

If you are developing your own theme/plugin/project which relies on this plugin, please be aware that any updates of the plugin may cause problems.

Once the plugin has stabilised and been published to GPM, we will follow semantic versioning and endeavour to keep the number of breaking changes to a minimum.

## Which Grav versions are supported?

Currently, the plugin is currently being developed on Grav version `1.6.23`. We recommend that you update your site to _at least this patch version_ in order to ensure compatibility.

> **NOTE:** the plugin has not been tested on Grav `1.7.x` as this version is still in beta. When it is released as stable, we will consider switching to this minor version.

## When should I use this plugin?

This plugin exposes a fairly comprehensive REST API to _manage a lot of your site's data_. This includes pages, media, users, plugins, and configuration files.

> **Note:** Grav already has [built-in support for custom Content Types](https://learn.getgrav.org/content/content-types), that would allow you to return JSON responses.
>
> If you only need to **retrieve page content**, you might find that creating your own Twig templates in your theme is a much better approach as you can customise the response to your exact requirements.

However, you may want to use this plugin if:

- You are using a pre-built theme and cannot extend it with custom page content types.
- You want to have your page content exposed as JSON, _but only for authenticated users_.
- You want to allow users to create/update page content without needing to give them access to the admin interface or the source files.
- You want to externally manage your site's, or any plugin's, configuration files.
- You want to be able to externally create Users on your site.

Some example use cases for this API:

- You want to create an authenticated section on your site that allows users to create/update pages, but only under a specific route, such as a "profile" section.
- You are building a Single Page Application in Javascript, perhaps with React or Vue.
- You have a lot of clients and want to create a portal where you can manage all your Grav site configurations from one place.
- You want to use an external registry of users and automatically create/remove user accounts whenever the registry changes.

## What endpoints are available?

Providing you have configured the plugin to enable the use of the endpoints, you can find an OpenApi specification in the [docs/specification.yaml](https://github.com/Regaez/grav-plugin-api/blob/master/docs/specification.yaml) file which details the paths and responses for each resource type. If you prefer a visual interface, you can also [view the specification in the SwaggerUI demo](http://petstore.swagger.io/?url=https://raw.githubusercontent.com/Regaez/grav-plugin-api/master/docs/specification.yaml), which you may find easier to read than the specification file itself.

We also provide a [Postman](https://www.postman.com/) collection in [docs/postman_collection.json](https://github.com/Regaez/grav-plugin-api/blob/master/docs/postman_collection.json) which can be imported into the application with example requests for all endpoints.

The API does provide some hypermedia linking with each response to aid in self-discovery of related resources, however (at this stage) it is not exhaustive.

## Why isn't Basic Authentication working for me?

Is your server configured to use PHP-CGI or PHP-FastCGI?

If so, then it's likely that your server is not attaching the header to the request when executing PHP thus the credential information is not available to the plugin. Please check out [the Authentication documentation for a suggested workaround.](https://github.com/Regaez/grav-plugin-api/blob/master/docs/AUTHENTICATION.md#php-cgi-and-php-fastcgi)

## How can I make my plugin/theme do something after an API request?

The Api plugin fires a number of custom events on every resource which your plugin, or theme, can hook onto and execute its own function on each occurence.

Take a look at [the Events documentation](https://github.com/Regaez/grav-plugin-api/blob/master/docs/EVENTS.md) for a full list of events and the data provided by the event.

**The event you want doesn't exist, or is not providing useful information?** Feel free to [open an issue](https://github.com/Regaez/grav-plugin-api/issues/new?assignees=&labels=feature&template=feature-request.md&title=feat%3A+your+idea); please explain your use case and why you think it would be valuable to be included.

## Does the Api plugin trigger [Git Sync](https://github.com/trilbymedia/grav-plugin-git-sync)?

No, the core Api plugin **does not** trigger any synchronisation with Git.

However, you can additionally install the [Api Git Sync Integrator Plugin](https://github.com/djairhogeuens/grav-plugin-api-git-sync-integrator), a third-party plugin developed by **@djairhogeuens**, which will hook onto the Api's events and automatically synchronise any time a resource is changed.
