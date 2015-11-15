![Earlsh - URL shortener](/blob/master/web/img/logo.png?raw=true "Earlsh - URL shortener")

Logo by: [Gergana Gergova](http://www.gerganagergova.net/)

An url shortener written in PHP using SQLite and the Symfony framework. The purpose of the application is to shorten long urls so that they are easier to remember and share. To achieve that it takes a URL of the form `http://www.something.com` and turns it into a token like `ab123`. Eventually such a token may be shared with an url like: `http://myhost.com/r/ab123`.

## Features

* Create and resolve shorturls
* Admin panel to remove shorturls
* REST-API to create and resolve URLs

## Run the demo

    git clone https://github.com/fzuellich/earlsh
    php app/...
    php app/console server:run

## Possible features

* Use API keys to authenticate incoming REST-API calls.

# REST-API

The project provides a JSON based REST API to create urls.

## Create url
**Endpoint:** `[PUT] api/url/create`

**Request:**
* _url_ - The url to shorten

**Response:**
* _token_ - The token created for the url.

**Example**

	# to server ==>
	{
		"url": "http://www.abc.de/index.php"
	}

	# <== response

	{
		"token": "12345678"
	}

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
