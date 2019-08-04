# Api Plugin

The **Api** Plugin is for [Grav CMS](http://github.com/getgrav/grav). It exposes a Rest API on top of your existing site in order to allow you to interact with your site programmatically.

## Installation

Installing the Api plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install api

This will install the Api plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/api`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `api`. You can find these files on [GitHub](https://github.com/regaez/grav-plugin-api) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/api

> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/api/api.yaml` to `user/config/plugins/api.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

## Usage

**Describe how to use the plugin.**

## Credits

**Did you incorporate third-party code? Want to thank somebody?**

## To Do

- [ ] Future plans, if any

## Development

If you want to develop, or contribute to the plugin, please refer to the [Development](https://github.com/Regaez/grav-plugin-api/tree/master/docs/DEVELOPMENT.md) readme in the Docs section.
