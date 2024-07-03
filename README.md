<p align="center">
    <img src="https://user-images.githubusercontent.com/8983173/143772621-444a7bad-7a74-450a-a5b4-59af00c57d60.png" alt="Gotenberg PHP Logo" width="150" height="140" />
    <h3 align="center">Gotenberg PHP</h3>
    <p align="center">A PHP client for interacting with Gotenberg</p>
    <p align="center">
        <a href="https://packagist.org/packages/gotenberg/gotenberg-php"><img alt="Latest Version" src="http://poser.pugx.org/gotenberg/gotenberg-php/v" /></a>
        <a href="https://packagist.org/packages/gotenberg/gotenberg-php"><img alt="Total Downloads" src="http://poser.pugx.org/gotenberg/gotenberg-php/downloads" /></a>
        <a href="https://github.com/gotenberg/gotenberg-php/actions/workflows/continuous_integration.yml"><img alt="Continuous Integration" src="https://github.com/gotenberg/gotenberg-php/actions/workflows/continuous_integration.yml/badge.svg" /></a>
        <a href="https://codecov.io/gh/gotenberg/gotenberg-php"><img alt="https://codecov.io/gh/gotenberg/gotenberg" src="https://codecov.io/gh/gotenberg/gotenberg-php/branch/main/graph/badge.svg" /></a>
    </p>
</p>

---

This package is a PHP client for [Gotenberg](https://gotenberg.dev), a developer-friendly API to interact with powerful 
tools like Chromium and LibreOffice for converting numerous document formats (HTML, Markdown, Word, Excel, etc.) into 
PDF files, and more!

⚠️ 

For **Gotenberg 6.x**, use [thecodingmachine/gotenberg-php-client](https://github.com/thecodingmachine/gotenberg-php-client) instead.

For **Gotenberg 7.x**, use version `v1.1.8`.

## Quick Examples

You may convert a target URL to PDF and save it to a given directory:

```php
use Gotenberg\Gotenberg;

// Converts a target URL to PDF and saves it to a given directory.
$filename = Gotenberg::save(
    Gotenberg::chromium($apiUrl)->pdf()->url('https://my.url'), 
    $pathToSavingDirectory
);
```

You may also convert Office documents and merge them:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

// Converts Office documents to PDF and merges them.
$response = Gotenberg::send(
    Gotenberg::libreOffice($apiUrl)
        ->merge()
        ->convert(
            Stream::path($pathToDocx),
            Stream::path($pathToXlsx)
        )
);
```

## Requirement

This packages requires [Gotenberg](https://gotenberg.dev), a Docker-powered stateless API for PDF files.

See the [installation guide](https://gotenberg.dev/docs/getting-started/installation) for more information.

## Installation

This package can be installed with Composer:

```
composer require gotenberg/gotenberg-php
```

We use *PSR-7* HTTP message interfaces (i.e., `RequestInterface` and `ResponseInterface`) and the *PSR-18* HTTP client
interface (i.e., `ClientInterface`).

For the latter, you may need an adapter in order to use your favorite client library. Check the available adapters:

* https://docs.php-http.org/en/latest/clients.html

If you're not sure which adapter you should use, consider using the `php-http/guzzle7-adapter`:

```
composer require php-http/guzzle7-adapter
```

## Send a request to the API

After having created the HTTP request (see below), you have two options:

1. Get the response from the API and handle it according to your need.
2. Save the resulting file to a given directory.

> In the following examples, we assume the Gotenberg API is available at http://localhost:3000.

### Get a response

You may use any HTTP client that is able to handle a *PSR-7* `RequestInterface` to call the API:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->pdf()
    ->url('https://my.url');
    
$response = $client->sendRequest($request);
```

If you have a *PSR-18* compatible HTTP client (see [Installation](#installation)), you may also use `Gotenberg::send`:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->pdf()
    ->url('https://my.url');

try {
    $response = Gotenberg::send($request);
    return $response;
} catch (GotenbergApiErrored $e) {
    // $e->getResponse();
}
```

This helper will parse the response and if it is not **2xx**, it will throw an exception. That's especially useful if 
you wish to return the response directly to the browser.

You may also explicitly set the HTTP client:

```php
use Gotenberg\Gotenberg;

$response = Gotenberg::send($request, $client);
```

### Save the resulting file

If you have a *PSR-18* compatible HTTP client (see [Installation](#installation)), you may use `Gotenberg::save`:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->pdf()
    ->url('https://my.url');
    
$filename = Gotenberg::save($request, '/path/to/saving/directory');
```

It returns the filename of the resulting file. By default, Gotenberg creates a *UUID* filename (i.e., 
`95cd9945-484f-4f89-8bdb-23dbdd0bdea9`) with either a `.zip` or a `.pdf` file extension.

You may also explicitly set the HTTP client:

```php
use Gotenberg\Gotenberg;

$response = Gotenberg::save($request, $pathToSavingDirectory, $client);
```

### Filename

You may override the output filename with:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->pdf()
    ->outputFilename('my_file')
    ->url('https://my.url');
```

Gotenberg will automatically add the correct file extension.

### Trace or request ID

By default, Gotenberg creates a *UUID* trace that identifies a request in its logs. You may override its value thanks to:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->pdf()
    ->trace('debug')
    ->url('https://my.url');
```

It will set the header `Gotenberg-Trace` with your value. You may also override the default header name:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->pdf()
    ->trace('debug', 'Request-Id')
    ->url('https://my.url');
```

Please note that it should be the same value as defined by the `--api-trace-header` Gotenberg's property.

The response from Gotenberg will also contain the trace header. In case of error, both the `Gotenberg::send` and 
`Gotenberg::save` methods throw a `GotenbergApiErroed` exception that provides the following method for retrieving the 
trace:

```php
use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Gotenberg;

try {
    $response = Gotenberg::send(
        Gotenberg::chromium('http://localhost:3000')
            ->screenshot()
            ->url('https://my.url')
    );
} catch (GotenbergApiErrored $e) {
    $trace = $e->getGotenbergTrace();
    // Or if you override the header name:
    $trace = $e->getGotenbergTrace('Request-Id');
}
```

## More

Head to the [documentation](https://gotenberg.dev/) to learn how to interact with Gotenberg 🚀
