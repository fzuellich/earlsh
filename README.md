# Earlsh - URL shortener
An url shortener written in PHP using SQLite and the Symfony framework.

## Features it offers

* Creation and solvation of URLs
* REST API to create and resolve URLs
* Admin panel to remove URLs
* Public mode enabling others to shorten URLs and share them

## Aim of the project

The purpose of the application is to shorten long urls so that they are easier to remember and share. To achieve that it takes a URL of the form `http://www.something.com` and turns it into `http://myhost.com/12345678`.

## Used technology

The technology behind the project is PHP using the Symfony framework. As database SQLite is used (even though a keystore database might make more sense for big installations).

The application is divided in four parts:
1. The internal API consisting of a database access layer and an entry point to resolve shortened URLs using a provided token.
2. A REST API which communication is based on JSON to shorten and resolve URLs.
3. A front-end to short URLs.
4. An administration panel to remove URLs.

## Features it doesn't offer (yet)

* A black list of URLs which should not be added to the service.
* A expiration function to remove shortened URLs after a fixed amount of time.

# API

The project provides a JSON based REST API to create urls.

## Create url
**Endpoint:** `[PUT] api/url/create`

**Request:**
* _url_ - The url to shorten

**Response:**
* _token_ - The token created for the url. `null` if the creation was not successful.

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