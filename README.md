<p align="center">
    <img src="https://raw.githubusercontent.com/gotenberg/art/master/php-logo.png" alt="Gotenberg PHP Logo" width="150" height="140" />
    <h3 align="center">Gotenberg PHP</h3>
    <p align="center">A PHP client for interacting with Gotenberg</p>
    <p align="center">
        <a href="https://packagist.org/packages/gotenberg/gotenberg-php"><img alt="Latest Version" src="https://poser.pugx.org/gotenberg/gotenberg-php/v" /></a>
        <a href="https://packagist.org/packages/gotenberg/gotenberg-php"><img alt="Total Downloads" src="https://poser.pugx.org/gotenberg/gotenberg-php/downloads" /></a>
        <a href="https://packagist.org/packages/gotenberg/gotenberg-php"><img alt="Monthly Downloads" src="https://poser.pugx.org/gotenberg/gotenberg-php/d/monthly" /></a>
        <a href="https://github.com/gotenberg/gotenberg-php/actions/workflows/continuous_integration.yml"><img alt="Continuous Integration" src="https://github.com/gotenberg/gotenberg-php/actions/workflows/continuous_integration.yml/badge.svg" /></a>
        <a href="https://codecov.io/gh/gotenberg/gotenberg-php"><img alt="https://codecov.io/gh/gotenberg/gotenberg" src="https://codecov.io/gh/gotenberg/gotenberg-php/branch/main/graph/badge.svg" /></a>
    </p>
</p>

---

This package is a PHP client for [Gotenberg](https://gotenberg.dev), a developer-friendly API to interact with powerful 
tools like Chromium and LibreOffice for converting numerous document formats (HTML, Markdown, Word, Excel, etc.) into 
PDF files, and more!

| Gotenberg version   | Client                                                                                            |
|---------------------|---------------------------------------------------------------------------------------------------|
| `9.x` **(current)** | `v3.x` **(current)**                                                                              |
| `8.x`               | `v2.x`                                                                                            |
| `7.x`               | `v1.x`                                                                                            |
| `6.x`               | [thecodingmachine/gotenberg-php-client](https://github.com/thecodingmachine/gotenberg-php-client) |

> [!TIP]
> A [Symfony Bundle](https://github.com/sensiolabs/GotenbergBundle) is also available!

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

You may also convert Office documents:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

// Converts Office documents to PDF.
$response = Gotenberg::send(
    Gotenberg::libreOffice($apiUrl)
        ->convert(
            Stream::path($pathToDocx),
            Stream::path($pathToXlsx)
        )
);
```

## Requirement

This package requires PHP 8.3 or later, and [Gotenberg](https://gotenberg.dev) 9, a containerized API for seamless PDF conversion.

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

## Build a request

This package is organized around *modules*, namely:

```php
use Gotenberg\Gotenberg;

Gotenberg::chromium($apiUrl);
Gotenberg::libreOffice($apiUrl);
Gotenberg::pdf($apiUrl);
```

Each of these modules offers a variety of methods to populate a *multipart/form-data* request.

After setting all optional form fields and files, you can create a request by calling the method that represents the endpoint. 
For example, to call the `/forms/chromium/convert/url` route:

```php
use Gotenberg\Gotenberg;

Gotenberg::chromium($apiUrl)
    ->pdf()                  // Or screenshot().
    ->singlePage()           // Optional.
    ->url('https://my.url');
```

> [!TIP]
> Head to the [documentation](https://gotenberg.dev/) to learn about all possibilities.

If the route requires form files, use the `Stream` class to create them:

```php
use Gotenberg\DownloadFrom;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

Gotenberg::libreOffice($apiUrl)
    ->convert(Stream::path($pathToDocument));

// Alternatively, you may also set the content directly.
Gotenberg::chromium($apiUrl)
    ->pdf()
    ->assets(Stream::string('style.css', 'body{font-family: Arial, Helvetica, sans-serif;}'))
    ->html(Stream::string('index.html', '<html><head><link rel="stylesheet" type="text/css" href="style.css"></head><body><p>Hello, world!</p></body></html>'));

// Or create your stream from scratch.
Gotenberg::libreOffice($apiUrl)
    ->convert(new Stream('document.docx', $stream));

// Or upload a ZIP archive that Gotenberg unpacks.
Gotenberg::chromium($apiUrl)
    ->pdf()
    ->archives(Stream::path($pathToSiteZip))
    ->html(null);

// Or even tell Gotenberg to download the files for you.
Gotenberg::libreOffice($apiUrl)
    ->downloadFrom(
        new DownloadFrom('https://url.to.document.docx', ['MyHeader' => 'MyValue'])
    )
    ->convert();
```

A *stage* is one operation on a PDF: merge, stamp, sign, make PDF/A, and so on. Gotenberg runs the stages you give,
in that order, on the PDFs a conversion makes:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Profile;
use Gotenberg\Stage;
use Gotenberg\Stream;

// Merges the documents, stamps a text on each page and makes the output PDF/A-2b.
Gotenberg::libreOffice($apiUrl)
    ->stages(
        Stage::merge(),
        Stage::stampText('DRAFT', x: 72, y: 72, fontSize: 24),
        Stage::remediate(Profile::PdfA2b),
    )
    ->convert(
        Stream::path($pathToDocx),
        Stream::path($pathToXlsx)
    );

// The pdf module runs stages on the PDFs you upload, through /forms/pdf/pipe...
Gotenberg::pdf($apiUrl)
    ->stages(
        Stage::select('1-2'),
        Stage::optimize(),
    )
    ->pipe(Stream::path($pathToPdf));

// ...or one stage through its own route, such as /forms/pdf/sign.
Gotenberg::pdf($apiUrl)
    ->run(Stage::sign(Stream::path($pathToP12), certificatePassword: 'secret'), Stream::path($pathToPdf));

// It also reads and validates PDFs; Gotenberg answers JSON.
Gotenberg::pdf($apiUrl)->metadata(Stream::path($pathToPdf));
Gotenberg::pdf($apiUrl)->validate([Profile::PdfA2b, Profile::PdfUa1], Stream::path($pathToPdf));
```

A stage that reads a file, such as an image to stamp or a certificate, takes a `Stream`: the client uploads it for you.
See [Run stages](https://gotenberg.dev/docs/pdf/pipe) for every stage and its order rules.

## Send a request to the API

After having created the HTTP request, you have two options:

1. Get the response from the API and handle it according to your need.
2. Save the resulting file to a given directory.

### Get a response

You may use any HTTP client that is able to handle a *PSR-7* `RequestInterface` to call the API:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->pdf()
    ->url('https://my.url');

$response = $client->sendRequest($request);
```

If you have a *PSR-18* compatible HTTP client (see [Installation](#installation)), you may also use `Gotenberg::send`:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
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

$request = Gotenberg::chromium($apiUrl)
    ->pdf()
    ->url('https://my.url');
    
$filename = Gotenberg::save($request, '/path/to/saving/directory');
```

It returns the filename of the resulting file, as the `Content-Disposition` header names it. Without
`outputFilename()`, Gotenberg names the result after its source (`report.docx` gives `report.docx.pdf`), or picks a
*UUID* for a web page or a ZIP archive.

You may also explicitly set the HTTP client:

```php
use Gotenberg\Gotenberg;

$response = Gotenberg::save($request, $pathToSavingDirectory, $client);
```

### Filename

You may override the output filename with:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->pdf()
    ->outputFilename('my_file')
    ->url('https://my.url');
```

Gotenberg will automatically add the correct file extension.

### Correlation ID

By default, Gotenberg creates a *UUID* correlation ID that identifies a request in its logs. You may override its value
thanks to:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->pdf()
    ->correlationId('debug')
    ->url('https://my.url');
```

It will set the header `Gotenberg-Trace` with your value. You may also override the default header name:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->pdf()
    ->correlationId('debug', 'Request-Id')
    ->url('https://my.url');
```

Please note that it should be the same value as defined by the `--api-correlation-id-header` Gotenberg's property.

The response from Gotenberg will also contain the correlation ID header. In case of error, both the `Gotenberg::send`
and `Gotenberg::save` methods throw a `GotenbergApiErrored` exception that provides the following method for retrieving
it:

```php
use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Gotenberg;

try {
    $response = Gotenberg::send(
        Gotenberg::chromium($apiUrl)
            ->screenshot()
            ->url('https://my.url')
    );
} catch (GotenbergApiErrored $e) {
    $correlationId = $e->getCorrelationId();
    // Or if you override the header name:
    $correlationId = $e->getCorrelationId('Request-Id');
}
```

### Webhook

You may tell Gotenberg to upload the result to a webhook instead of answering with it. Gotenberg then sends a
`webhook.success` or `webhook.error` event to the events URL:

```php
use Gotenberg\Gotenberg;
use Gotenberg\WebhookMethod;

$request = Gotenberg::chromium($apiUrl)
    ->pdf()
    ->webhook('https://my.webhook.url', 'https://my.webhook.url/events')
    ->webhookMethod(WebhookMethod::Put) // Optional, default POST.
    ->webhookExtraHttpHeaders(['Authorization' => 'Bearer …']) // Optional.
    ->url('https://my.url');
```
