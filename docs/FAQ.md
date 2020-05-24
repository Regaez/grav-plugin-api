# Frequently Asked Questions

- [Why isn't Basic Authentication working for me?](#why-isnt-basic-authentication-working-for-me)
- [How can I make my plugin/theme do something after an API request?](#how-can-i-make-my-plugintheme-do-something-after-an-api-request)
- [Does the Api plugin trigger Git Sync?](#does-the-api-plugin-trigger-git-sync)

## Why isn't Basic Authentication working for me?

Is your server configured to use PHP-CGI or PHP-FastCGI?

If so, then it's likely that your server is not attaching the header to the request when executing PHP thus the credential information is not available to the plugin. Please check out [the Authentication documentation for a suggested workaround.](https://github.com/Regaez/grav-plugin-api/blob/master/docs/AUTHENTICATION.md#php-cgi-and-php-fastcgi)

## How can I make my plugin/theme do something after an API request?

The Api plugin fires a number of custom events on every resource which your plugin, or theme, can hook onto and execute its own function on each occurence.

Take a look at [the Events documentation](https://github.com/Regaez/grav-plugin-api/blob/master/docs/EVENTS.md) for a full list of events and the data provided by the event.

**The event you want doesn't exist, or is not providing useful information?** Feel free to [open an issue](https://github.com/Regaez/grav-plugin-api/issues/new?assignees=&labels=feature&template=feature-request.md&title=feat%3A+your+idea); please explain your use case and why you think it would be valuable to be included.

## Does the Api plugin trigger [Git Sync](https://github.com/trilbymedia/grav-plugin-git-sync)?

No, the core Api plugin **does not** trigger any synchronisation with Git.

However, you can install the [Api Git Sync Integrator Plugin](https://github.com/djairhogeuens/grav-plugin-api-git-sync-integrator), a third-party plugin developed by @djairhogeuens, which will hook onto the Api's events and will automatically synchronise any time a resource is changed.
