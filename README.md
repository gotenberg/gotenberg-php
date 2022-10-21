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

⚠️ For **Gotenberg 6.x**, use [thecodingmachine/gotenberg-php-client](https://github.com/thecodingmachine/gotenberg-php-client) instead.

## Quick Examples

You may convert a target URL to PDF and save it to a given directory:

```php
use Gotenberg\Gotenberg;

// Converts a target URL to PDF and saves it to a given directory.
$filename = Gotenberg::save(
    Gotenberg::chromium($apiUrl)->url('https://my.url'), 
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

This packages requires [Gotenberg](https://gotenberg.dev), a Docker-powered stateless API for PDF files:

* 🔥 [Live Demo](https://gotenberg.dev/docs/get-started/live-demo)
* [Docker](https://gotenberg.dev/docs/get-started/docker)
* [Docker Compose](https://gotenberg.dev/docs/get-started/docker-compose)
* [Kubernetes](https://gotenberg.dev/docs/get-started/kubernetes)
* [Cloud Run](https://gotenberg.dev/docs/get-started/cloud-run)

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

## Usage

* [Send a request to the API](#send-a-request-to-the-api)
* [Chromium](#chromium)
* [LibreOffice](#libreOffice)
* [PDF Engines](#pdf-engines)
* [Webhook](#webhook)

### Send a request to the API

After having created the HTTP request (see below), you have two options:

1. Get the response from the API and handle it according to your need.
2. Save the resulting file to a given directory.

> In the following examples, we assume the Gotenberg API is available at http://localhost:3000.

#### Get a response

You may use any HTTP client that is able to handle a *PSR-7* `RequestInterface` to call the API:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->url('https://my.url');
    
$response = $client->sendRequest($request);
```

If you have a *PSR-18* compatible HTTP client (see [Installation](#installation)), you may also use `Gotenberg::send`:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->url('https://my.url');

try {
    $response = Gotenberg::send($request);
    return $response;
} catch (GotenbergApiErroed $e) {
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

#### Save the resulting file

If you have a *PSR-18* compatible HTTP client (see [Installation](#installation)), you may use `Gotenberg::save`:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
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

#### Filename

You may override the output filename with:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->outputFilename('my_file')
    ->url('https://my.url');
```

Gotenberg will automatically add the correct file extension.

#### Trace or request ID

By default, Gotenberg creates a *UUID* trace that identifies a request in its logs. You may override its value thanks to:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->trace('debug')
    ->url('https://my.url');
```

It will set the header `Gotenberg-Trace` with your value. You may also override the default header name:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium('http://localhost:3000')
    ->trace('debug', 'Request-Id')
    ->url('https://my.url');
```

Please note that it should be the same value as defined by the `--api-trace-header` Gotenberg's property.

The response from Gotenberg will also contain the trace header. In case of error, both the `Gotenberg::send` and 
`Gotenberg::save` methods throw a `GotenbergApiErroed` exception that provides the following method for retrieving the 
trace:

```php
use Gotenberg\Exceptions\GotenbergApiErroed;
use Gotenberg\Gotenberg;

try {
    $response = Gotenberg::send(
        Gotenberg::chromium('http://localhost:3000')
            ->url('https://my.url')
    );
} catch (GotenbergApiErroed $e) {
    $trace = $e->getGotenbergTrace();
    // Or if you override the header name:
    $trace = $e->getGotenbergTrace('Request-Id');
}
```

### Chromium

The [Chromium module](https://gotenberg.dev/docs/modules/chromium) interacts with the Chromium browser to convert HTML documents to PDF.

#### Convert a target URL to PDF

See https://gotenberg.dev/docs/modules/chromium#url.

Converting a target URL to PDF is as simple as:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->url('https://my.url');
```

You may inject `<link>` and `<script>` HTML elements thanks to the `$extraLinkTags` and `$extraScriptTags` arguments:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Modules\ChromiumExtraLinkTag;
use Gotenberg\Modules\ChromiumExtraScriptTag;

$request = Gotenberg::chromium($apiUrl)
    ->url(
        'https://my.url',
        [
            new ChromiumExtraLinkTag('https://my.css'),
        ],
        [
            new ChromiumExtraScriptTag('https://my.js'),
        ],
    );
```

Please note that Gotenberg will add the `<link>` and `<script>` elements based on the order of the arguments.

#### Convert an HTML document to PDF

See https://gotenberg.dev/docs/modules/chromium#html.

You may convert an HTML document with:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::chromium($apiUrl)
    ->html(Stream::path('/path/to/file.html'));
```

Or with an HTML string:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::chromium($apiUrl)
    ->html(Stream::string('my.html', $someHtml));
```

Please note that it automatically sets the filename to `index.html`, as required by Gotenberg, whatever the value you're
using with the `Stream` class.

You may also send additional files, like images, fonts, stylesheets, and so on. The only requirement is that their paths
in the HTML DOM are on the root level.

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::chromium($apiUrl)
    ->assets(
        Stream::path('/path/to/my.css'),
        Stream::path('/path/to/my.js')
    )
    ->html(Stream::path('/path/to/file.html'));
```

#### Convert one or more markdown files to PDF

See https://gotenberg.dev/docs/modules/chromium#markdown.

You may convert markdown files with:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::chromium($apiUrl)
    ->markdown(
        Stream::path('/path/to/my_wrapper.html'),
        Stream::path('/path/to/file.md')
    );
```

The first argument is a `Stream` with HTML content, for instance:

```html
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>My PDF</title>
  </head>
  <body>
    {{ toHTML "file.md" }}
  </body>
</html>
```

Here, there is a Go template function `toHTML`. Gotenberg will use it to convert a markdown file's content to HTML.

Like the HTML conversion, you may also send additional files, like images, fonts, stylesheets, and so on. The only 
requirement is that their paths in the HTML DOM are on the root level.

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::chromium($apiUrl)
    ->assets(
        Stream::path('/path/to/my.css'),
        Stream::path('/path/to/my.js')
    )
    ->markdown(
        Stream::path('/path/to/file.html'),
        Stream::path('/path/to/my.md'),
        Stream::path('/path/to/my2.md')
    );
```

#### Paper size

You may override the default paper size with:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->paperSize($width, $height)
    ->url('https://my.url');
```

Examples of paper size (width x height, in inches):

* `Letter` - 8.5 x 11 (default)
* `Legal` - 8.5 x 14
* `Tabloid` - 11 x 17
* `Ledger` - 17 x 11
* `A0` - 33.1 x 46.8
* `A1` - 23.4 x 33.1
* `A2` - 16.54 x 23.4
* `A3` - 11.7 x 16.54
* `A4` - 8.27 x 11.7
* `A5` - 5.83 x 8.27
* `A6` - 4.13 x 5.83

#### Margins

You may override the default margins (i.e., `0.39`, in inches):

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->margins($top, $bottom, $left, $right)
    ->url('https://my.url');
```

#### Prefer CSS page size

You may force page size as defined by CSS:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->preferCssPageSize()
    ->url('https://my.url');
```

#### Print the background graphics

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->printBackground()
    ->url('https://my.url');
```

You may also hide the default white background and allow generating PDFs with transparency with:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->printBackground()
    ->omitBackground()
    ->url('https://my.url');
```

The rules regarding the `printBackground` and `omitBackground` form fields are the following:

* If `printBackground` is set to *false*, no background is printed.
* If `printBackground` is set to *true*:
    * If the HTML document has a background, that background is used.
    * If not:
        * If `omitBackground` is set to *true*, the default background is transparent.
        * If not, the default white background is used.

#### Landscape orientation

You may override the default portrait orientation with:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->landscape()
    ->url('https://my.url');
```

#### Scale

You may override the default scale of the page rendering (i.e., `1.0`) with:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->scale(2.0)
    ->url('https://my.url');
```

#### Page ranges

You may set the page ranges to print, e.g., `1-5, 8, 11-13`. Empty means all pages.

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->nativePageRanges('1-2')
    ->url('https://my.url');
```

#### Header and footer

You may add a header and/or a footer to each page of the PDF:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::chromium($apiUrl)
    ->header(Stream::path('/path/to/my_header.html'))
    ->footer(Stream::path('/path/to/my_footer.html'))
    ->margins(1, 1, 0.39, 0.39)
    ->url('https://my.url');
```

Please note that it automatically sets the filenames to `header.html` and `footer.html`, as required by Gotenberg, 
whatever the value you're using with the `Stream` class.

Each of them has to be a complete HTML document:

```html
<html>
<head>
    <style>
    body {
        font-size: 8rem;
        margin: 4rem auto;
    }
    </style>
</head>
<body>
<p><span class="pageNumber"></span> of <span class="totalPages"></span></p>
</body>
</html>
```

The following classes allow you to inject printing values:

* `date` - formatted print date.
* `title` - document title.
* `url` - document location.
* `pageNumber` - current page number.
* `totalPages` - total pages in the document.

⚠️ Make sure that:

1. Margins top and bottom are large enough (i.e., `->margins(1, 1, 0.39, 0.39)`)
2. The font size is big enough.

⚠️ There are some limitations:

* No JavaScript.
* The CSS properties are independent of the ones from the HTML document.
* The footer CSS properties override the ones from the header;
* Only fonts installed in the Docker image are loaded - see the [Fonts chapter](https://gotenberg.dev/docs/customize/fonts).
* Images only work using a base64 encoded source - i.e., `data:image/png;base64, iVBORw0K....`
* `background-color` and color `CSS` properties require an additional `-webkit-print-color-adjust: exact` CSS property in order to work.
* Assets are not loaded (i.e., CSS files, scripts, fonts, etc.).
* Background form fields do not apply.

#### Wait delay

When the page relies on JavaScript for rendering, and you don't have access to the page's code, you may want to wait a
certain amount of time (i.e., `1s`, `2ms`, etc.) to make sure Chromium has fully rendered the page you're trying to generate.

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->waitDelay('3s')
    ->url('https://my.url');
```

#### Wait for expression

You may also wait until a given JavaScript expression returns true:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->waitForExpression("window.status === 'ready'")
    ->url('https://my.url');
```

#### User agent

You may override the default `User-Agent` header used by Gotenberg:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->userAgent("Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1")
    ->url('https://my.url');
```

#### Extra HTTP headers

You may add HTTP headers that Chromium will send when loading the HTML document:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->extraHttpHeaders([
        'My-Header-1' => 'My value',
        'My-Header-2' => 'My value'
    ])
    ->url('https://my.url');
```

#### Fail on console exceptions

You may force Gotenberg to return a `409 Conflict` response if there are exceptions in the Chromium console:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->failOnConsoleExceptions()
    ->url('https://my.url');
```

#### Emulate media type

Some websites have dedicated CSS rules for print. Using `screen` allows you to force the "standard" CSS rules:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->emulateScreenMediaType()
    ->url('https://my.url');
```

You may also force the `print` media type with:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->emulatePrintMediaType()
    ->url('https://my.url');
```

#### PDF Format

See https://gotenberg.dev/docs/modules/pdf-engines#engines.

You may set the PDF format of the resulting PDF with:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->pdfFormat('PDF/A-1a')
    ->url('https://my.url');
```

### LibreOffice

The [LibreOffice module](https://gotenberg.dev/docs/modules/libreoffice) interacts with [LibreOffice](https://www.libreoffice.org/) 
to convert documents to PDF, thanks to [unoconv](https://github.com/unoconv/unoconv).

#### Convert documents to PDF

See https://gotenberg.dev/docs/modules/libreoffice#route.

Converting a document to PDF is as simple as:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::libreOffice($apiUrl)
    ->convert(Stream::path('/path/to/my.docx'));
```

If you send many documents, Gotenberg will return a ZIP archive with the PDFs:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::libreOffice($apiUrl)
    ->outputFilename('archive')
    ->convert(
        Stream::path('/path/to/my.docx'),
        Stream::path('/path/to/my.xlsx')
    );

// $filename = archive.zip
$filename = Gotenberg::save($request, $pathToSavingDirectory);
```

You may also merge them into one unique PDF:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::libreOffice($apiUrl)
    ->merge()
    ->outputFilename('merged')
    ->convert(
        Stream::path('/path/to/my.docx'),
        Stream::path('/path/to/my.xlsx')
    );

// $filename = merged.pdf
$filename = Gotenberg::save($request, $pathToSavingDirectory);
```

Please note that the merging order is determined by the order of the arguments.

#### Landscape orientation

You may override the default portrait orientation with:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::libreOffice($apiUrl)
    ->landscape()
    ->convert(Stream::path('/path/to/my.docx'));
```


#### Page ranges

You may set the page ranges to print, e.g., `1-4`. Empty means all pages.

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::libreOffice($apiUrl)
    ->nativePageRanges('1-2')
    ->convert(Stream::path('/path/to/my.docx'));
```

⚠️ The page ranges are applied to all files independently.

#### PDF format

See https://gotenberg.dev/docs/modules/pdf-engines#engines.

You may set the PDF format of the resulting PDF(s) with:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::libreOffice($apiUrl)
    ->pdfFormat('PDF/A-1a')
    ->convert(Stream::path('/path/to/my.docx'));
```

You may also explicitly tell Gotenberg to use [unoconv](https://github.com/unoconv/unoconv) to convert the resulting 
PDF(s) to a PDF format:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::libreOffice($apiUrl)
    ->nativePdfFormat('PDF/A-1a')
    ->convert(Stream::path('/path/to/my.docx'));
```

⚠️ You cannot set both property, otherwise Gotenberg will return `400 Bad Request` response.

### PDF Engines

The [PDF Engines module](https://gotenberg.dev/docs/modules/pdf-engines) gathers all engines that can manipulate PDF files.

#### Merge PDFs

See https://gotenberg.dev/docs/modules/pdf-engines#merge.

Merging PDFs is as simple as:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::pdfEngines($apiUrl)
    ->merge(
        Stream::path('/path/to/my.pdf'),
        Stream::path('/path/to/my2.pdf')
    );
```

Please note that the merging order is determined by the order of the arguments.

You may also set the PDF format of the resulting PDF(s) with:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::pdfEngines($apiUrl)
    ->pdfFormat('PDF/A-1a')
    ->merge(
        Stream::path('/path/to/my.pdf'),
        Stream::path('/path/to/my2.pdf'),
        Stream::path('/path/to/my3.pdf')
    );
```

#### Convert to a specific PDF format

See https://gotenberg.dev/docs/modules/pdf-engines#convert.

You may convert a PDF to a specific PDF format with:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::pdfEngines($apiUrl)
    ->convert(
        'PDF/A-1a'
        Stream::path('/path/to/my.pdf')
    );
```

If you send many PDFs, Gotenberg will return a ZIP archive with the PDFs:

```php
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

$request = Gotenberg::pdfEngines($apiUrl)
    ->outputFilename('archive')
    ->convert(
        'PDF/A-1a',
        Stream::path('/path/to/my.pdf'),
        Stream::path('/path/to/my2.pdf'),
        Stream::path('/path/to/my3.pdf')
    );

// $filename = archive.zip
$filename = Gotenberg::save($request, $pathToSavingDirectory);
```

### Webhook

The [Webhook module](https://gotenberg.dev/docs/modules/webhook) is a Gotenberg middleware that sends the API
responses to callbacks.

⚠️ You cannot use the `Gotenberg::save` method if you're using the webhook feature.

For instance:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->webhook('https://my.webhook.url', 'https://my.webhook.error.url')
    ->url('https://my.url'); 
```

You may also override the default HTTP method (`POST`) that Gotenberg will use to call the webhooks:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->webhook('https://my.webhook.url', 'https://my.webhook.error.url')
    ->webhookMethod('PATCH')
    ->webhookErrorMethod('PUT')
    ->url('https://my.url');
```

You may also tell Gotenberg to add extra HTTP headers that it will send alongside the request to the webhooks:

```php
use Gotenberg\Gotenberg;

$request = Gotenberg::chromium($apiUrl)
    ->webhook('https://my.webhook.url', 'https://my.webhook.error.url')
    ->webhookExtraHttpHeaders([
        'My-Header-1' => 'My value',
        'My-Header-2' => 'My value'    
    ])
    ->url('https://my.url');
```
