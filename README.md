![Earlsh - URL shortener](./web/img/logo.png?raw=true "Earlsh - URL shortener")

Logo by: [Gergana Gergova](http://www.gerganagergova.net/)

An URL shortener written in PHP using SQLite and the Symfony framework. The purpose of the application is to shorten long urls so that they are easier to remember and share. To achieve that it takes a URL of the form `http://www.something.com` and turns it into a token like `ab123`. Eventually such a token may be shared with an URL like: `http://myhost.com/r/ab123`.

## Features

* Create and resolve shorturls
* Admin panel to remove shorturls
* REST-API to create and resolve URLs
* Define regular expressions to reject URLs from shortening

## Run the app

    git clone https://github.com/fzuellich/earlsh
    composer install

    # define parameter 'database_path', for example "%kernel.root_dir%/data/database.db", in app/config/parameters.yml
    # make sure the path exists to the database

    php app/console doctrine:database:create
    php app/console doctrine:schema:create

    # apply some post-deployment tasks like described here
    # http://symfony.com/doc/2.8/cookbook/deployment/tools.html#common-post-deployment-tasks

    php app/console server:run

# Configuration

The installation can be configured using the earlsh.yml configuration file located in `app/config/`.

## `prevent_local_urls`

Prevents a user from shortening urls pointing to the url the application is hosted on. This maybe useful if you don't want endless redirection loops in case the user can guess the next token and create a short url like this:

1. Current token is a.
2. User shortens url http://host/r/b and gets the token b.
3. Resolving 'b' now gives us the url http://host/r/b and thus creates an endless loop.

__Set the configuration parameter `hostname` to make this work!__

## `hostname`

Because it is not really trivial to resolve the current hostname of the application you need to provide this yourself!

The value should just be the domain name and is later extended to a regular expression of the form `#{hostname}/r`.

## `rejected_sites`

In order to block certain URLs from being shortened, you may define a list of regular expressions that will be used to evaluate URLs.

The value should be the pattern without the delimiters required by PHP (e.g. `#` or `/`). These are later converted to a regular expression like: `#{yourpattern}#i`. _Therefore the patterns are case-insensitive!_

__In case the regular expression creates an error, this information is written to the error log using the function `error_log`.__

# REST-API

The project provides a JSON based REST API to create urls.

## Authentication using API Keys

API keys allow to limit the usage of the REST-API. They should only be used in
combination with HTTPS. They can be generated in the admin interface and have to be
supplied in the request as 'apikey' GET parameter.

## Create url

**Endpoint:** `[PUT] api/url/create?apikey=%apikey%`

**Request:**
* _url_ - The url to shorten

**Response:**
* _token_ - The token created for the url.

**Example**

	# to server ==>
	{ "url": "http://www.abc.de/index.php" }

	# <== response
	{ "token": "12345678" }


## Resolve url

**Endpoint:** `[GET] api/url/resolve/{token:string}`

**Request:**

* _token_ - The token to resolve.

**Response:**

* _url_ - The url resolved.


## Exceptions

When calling the REST-API with illegal arguments or if otherwise an internal error occurs, there might be an exception returned. The format for exception reporting is always:

    {
        "exception": "ExceptionName",
        "message": "Information about the exception."
    }
