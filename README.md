# Aatis HTTP Foundation

## About

Http foundation package is an additionnal layer that replaces the use of the global variables.

## Installation

```bash
composer require aatis/http-foundation
```

## Usage

### ParameterBag

The `ParameterBag` class is a custom array with the following methods:

- `has($key)` to check if a key exists
- `get($key)` to get a value
- `all()` to get all values as an array
- `set($key, $value)` to set a value
- `add($key, $value)` to add a value to an existing or non existing key
- `remove($key)` to remove a key

### HeaderBag

The `HeaderBag` class is a `ParameterBag` with case insensitive keys.

### Message

The `Message` principle is the base class for the `Request` and `Response` classes.

It contains the following properties:

- headers that is an `HeaderBag` of headers.
- content that is a string.
- protocolVersion that is the version of HTTP used by the server.

### Request

To create a request, you can use the static `createFromGlobals()` method of the `Request` class.

```php
$request = Request::createFromGlobals();
```

### Response

To create a response, you must precise a content which must be a string.
You can also precise optionals status code and/or headers.

By default, the status code is `200` and the headers are empty.

```php
$response = new Response('Hello, World!');
```

It is possible to model the response on a request by using the `prepare()` method, which will copy the protocol version and the headers of the `Request` that are not already defined.

```php
$request = Request::createFromGlobals();
$response = new Response('Hello, World!')->prepare($request);
```

### JsonResponse

The `JsonResponse` class is a `Response` with a JSON content.
It will automatically set the `Content-Type` header to `application/json` and encode the content given to json.

```php
$response = new JsonResponse(['message' => 'Hello, World!']);
```
