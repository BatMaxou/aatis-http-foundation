# Aatis HTTP Foundation

## About

Http foundation package is an additionnal layer that replaces the use of the global variables.

## Installation

```bash
composer require aatis/http-foundation
```

## Usage

### File

The `File` class is a representation of a file with it stream resource and the basic methods of the `SplFileInfo` class.

It implements the `FileInterface` interface that contains the additionnal following methods:

- `detach()` to detach the stream resource
- `close()` to close the stream
- `tell()` to get the current position into the stream
- `eof()` to check if the end of the stream has been reached
- `seek($offset, $whence = SEEK_SET)` to move the position into the stream
- `rewind()` to move the position to the beginning of the stream
- `read($length)` to read a part of the stream
- `getStream()` to get the stream resource
- `setOverrideName($fileName)` to override the name of the file into the class
- `write($string)` to write a string into the stream at the current position
- `append($string)` to write a string at the end of the stream
- `save($path)` to save the content of the stream into a file at the given path
- `getContents()` to get the content of the stream as a string

### UploadedFile

`UploadedFile` is a `File` but with the full name of the file into the constructor.

> [!NOTE]
> Useful to handle $\_FILES and tmp files for example.

### ParameterBag

The `ParameterBag` class is a custom array with the following methods:

- `has($key)` to check if a key exists
- `get($key)` to get a value
- `all()` to get all values as an array
- `set($key, $value)` to set a value
- `add($key, $value)` to add a value to an existing or non existing key
- `remove($key)` to remove a key

### HeaderBag

`HeaderBag` is a `ParameterBag` with case insensitive keys.

### ServerBag

`ServerBag` is a `ParameterBag` with an additionnal method `getHeaders()` that returns an array of all the headers beginning with `HTTP_` (exepct `HTTP_COOKIE`).

### CookieBag

`CookieBag` is a `ParameterBag` with an additionnal method `getInline()` that returns a string of all the cookies in a format that can be used in a `Set-Cookie` header.

### UploadedFileBag

`UploadedFileBag` is a `ParameterBag` that only contains `UploadedFile` objects.

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

> [!NOTE]
> By default, the status code is `200` and the headers are empty.

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

### RedirectResponse

The `RedirectResponse` class is a `Response` with a redirection.
It will automatically set the `Location` header to the given URL.

> [!NOTE]
> The status code is `301` by default.

```php
$response = new RedirectResponse('https://github.com/BatMaxou/aatis-http-foundation');
```

### FileResponse

The `FileResponse` class is a `Response` wich take a `FileInterface` or the path of a file as content. It will automatically set:

- the Content-Type header to the mime type of the file
- the Content-Lenght header to the size of the file
- the Content-Disposition header with the filename of the file

```php
$response = new FileResponse('path/to/file');

// OR

$file = new File('path/to/file');
$response = new FileResponse($file);
```
