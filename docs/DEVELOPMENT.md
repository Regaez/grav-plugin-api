# Api Plugin Development

## Setup

You will need to install the following:

- [PHP >7.2](https://www.php.net/downloads.php)
- [composer](https://getcomposer.org/download/) (set up so you can simply [run `composer` anywhere](https://getcomposer.org/doc/00-intro.md#globally))
- [docker + docker-compose](https://docs.docker.com/compose/install/)


## Local development

For ease of use, the project is set up to run Grav in Docker. The project layout is such:

```
docs/                 # API specification, Postman collection, etc.

grav/                 # This folder contains Grav "user" subdirectories
    accounts/         # so that we can maintain consistent site data
    pages/            # for development and testing purposes.

src/                  # The code for the API app.
tests/                # PHPUnit testing code.
vendor/               # Composer dependencies, ignored by git.
```

We use `docker-compose` to run the Docker image and map the necessary local Grav directories as volumes to the container. This allows us to preserve state between container restarts, as well as easily share site content between project collaborators as it will be change-tracked by `git`.

Once you've started the local development environment, you can view the API by requesting: `http://localhost:8080/api`


### Scripts

There are a number of scripts set up to make development easier:

- `composer start`: Runs the docker development environment.
- `composer stop`: Stops the docker development environment.
- `composer lint`: Checks the project source code for PHP style errors.
- `composer lint:fix`: Attempts to resolve any linting errors if possible.
- `composer test`: Runs the unit tests.
- `composer docker:clean`: Deletes the existing docker development environment.


### User Accounts

There are a few different dummy user accounts available for local testing. These accounts can be used to authenticate API requests using the `Basic` authentication type.


#### Admin Credentials

If you need to log into the grav admin interface for the local development environment, the following credentials are valid with _admin.super_ privileges (which also allows access to all API resources):

> **Username:** `development`
>
> **Password:** `D3velopment`


#### Testing Permissions

The following accounts have various levels role permissions set. For simplicity, they all share the same password as the **admin account above**:

---

> **Username:** `percy`

_Percy "Permit All"_ can access all resources in the API. The account includes the following roles:

- `api.pages_read`
- `api.pages_delete`
- `api.pages_edit`
- `api.pages_create`
- `api.users_read`
- `api.users_delete`
- `api.users_create`
- `api.users_edit`
- `api.plugins_read`
- `api.plugins_edit`
- `api.plugins_install`
- `api.plugins_uninstall`
- `api.configs_read`
- `api.configs_edit`
- `admin.login`
- `site.login`

---

> **Username:** `joe`

_"No-go" Joe_ cannot do anything with the API where authentication is required. THe account includes the following roles:

- `admin.login`
- `site.login`

---

> **Username:** `andy`

_"Admin" Andy_ inherits his permissions from the Grav group `siteadmins`. The _group_ includes the following roles:

- `api.users_read`
- `api.users_delete`
- `api.users_create`
- `api.users_edit`
- `api.plugins_read`
- `api.plugins_edit`
- `api.plugins_install`
- `api.plugins_uninstall`
- `api.configs_read`
- `api.configs_edit`
- `admin.login`
- `site.login`
