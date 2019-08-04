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

### Admin Credentials

If you need to log into the grav admin interface for the local development environment, the default credentials are valid with _administrator_ privileges:

> **Username:** `development`
>
> **Password:** `D3velopment`
