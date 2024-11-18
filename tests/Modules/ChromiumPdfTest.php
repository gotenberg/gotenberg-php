<?php

declare(strict_types=1);

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Gotenberg;
use Gotenberg\Modules\ChromiumCookie;
use Gotenberg\Modules\ChromiumPdf;
use Gotenberg\Stream;

it(
    'creates a valid request for the "/forms/chromium/convert/url" endpoint',
    /**
     * @param ChromiumCookie[] $cookies
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param int[] $failOnResourceHttpStatusCodes
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[] $assets
     */
    function (
        string $url,
        bool $singlePage = false,
        float|string|null $paperWidth = null,
        float|string $paperHeight = 0,
        float|string|null $marginTop = null,
        float|string $marginBottom = 0,
        float|string $marginLeft = 0,
        float|string $marginRight = 0,
        bool $preferCssPageSize = false,
        bool $generateDocumentOutline = false,
        bool $printBackground = false,
        bool $omitBackground = false,
        bool $landscape = false,
        float|null $scale = null,
        string|null $nativePageRanges = null,
        Stream|null $header = null,
        Stream|null $footer = null,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $emulatedMediaType = null,
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->pdf();
        $chromium = hydrateChromiumPdfFormData(
            $chromium,
            $singlePage,
            $paperWidth,
            $paperHeight,
            $marginTop,
            $marginBottom,
            $marginLeft,
            $marginRight,
            $preferCssPageSize,
            $generateDocumentOutline,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
            $metadata,
            $assets,
        );

        $request = $chromium->url($url);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/chromium/convert/url');
        expect($body)->toContainFormValue('url', $url);

        expectChromiumPdfOptions(
            $body,
            $singlePage,
            $paperWidth,
            $paperHeight,
            $marginTop,
            $marginBottom,
            $marginLeft,
            $marginRight,
            $preferCssPageSize,
            $generateDocumentOutline,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
            $metadata,
            $assets,
        );
    },
)->with([
    ['https://my.url'],
    ['https://my.url'],
    [
        'https://my.url',
        true,
        8.27,
        11.7,
        '192px',
        2,
        2,
        2,
        true,
        true,
        true,
        true,
        true,
        1.0,
        '1-2',
        Stream::string('my_header.html', 'Header content'),
        Stream::string('my_footer.html', 'Footer content'),
        '1s',
        "window.status === 'ready'",
        'print',
        [
            new ChromiumCookie('yummy_cookie', 'choco', 'theyummycookie.com'),
            new ChromiumCookie('vanilla_cookie', 'vanilla', 'theyummycookie.com', '/', true, true, 'Lax'),
        ],
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko)',
        [
            'My-Http-Header' => 'HTTP Header content',
            'My-Second-Http-Header' => 'Second HTTP Header content',
        ],
        [ 499, 599 ],
        [ 499, 599 ],
        true,
        true,
        true,
        'PDF/A-1a',
        true,
        [ 'Producer' => 'Gotenberg' ],
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

it(
    'creates a valid request for the "/forms/chromium/convert/html" endpoint',
    /**
     * @param ChromiumCookie[] $cookies
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param int[] $failOnResourceHttpStatusCodes
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[] $assets
     */
    function (
        Stream $index,
        bool $singlePage = false,
        float|string|null $paperWidth = null,
        float|string $paperHeight = 0,
        float|string|null $marginTop = null,
        float|string $marginBottom = 0,
        float|string $marginLeft = 0,
        float|string $marginRight = 0,
        bool $preferCssPageSize = false,
        bool $generateDocumentOutline = false,
        bool $printBackground = false,
        bool $omitBackground = false,
        bool $landscape = false,
        float|null $scale = null,
        string|null $nativePageRanges = null,
        Stream|null $header = null,
        Stream|null $footer = null,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $emulatedMediaType = null,
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->pdf();
        $chromium = hydrateChromiumPdfFormData(
            $chromium,
            $singlePage,
            $paperWidth,
            $paperHeight,
            $marginTop,
            $marginBottom,
            $marginLeft,
            $marginRight,
            $preferCssPageSize,
            $generateDocumentOutline,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
            $metadata,
            $assets,
        );

        $request = $chromium->html($index);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/chromium/convert/html');

        $index->getStream()->rewind();
        expect($body)->toContainFormFile('index.html', $index->getStream()->getContents(), 'text/html');

        expectChromiumPdfOptions(
            $body,
            $singlePage,
            $paperWidth,
            $paperHeight,
            $marginTop,
            $marginBottom,
            $marginLeft,
            $marginRight,
            $preferCssPageSize,
            $generateDocumentOutline,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
            $metadata,
            $assets,
        );
    },
)->with([
    [Stream::string('my.html', 'HTML content')],
    [
        Stream::string('my.html', 'HTML content'),
        true,
        8.27,
        11.7,
        '192px',
        2,
        2,
        2,
        true,
        true,
        true,
        true,
        true,
        1.0,
        '1-2',
        Stream::string('my_header.html', 'Header content'),
        Stream::string('my_footer.html', 'Footer content'),
        '1s',
        "window.status === 'ready'",
        'screen',
        [
            new ChromiumCookie('yummy_cookie', 'choco', 'theyummycookie.com'),
            new ChromiumCookie('vanilla_cookie', 'vanilla', 'theyummycookie.com', '/', true, true, 'Lax'),
        ],
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko)',
        [
            'My-Http-Header' => 'Http Header content',
            'My-Second-Http-Header' => 'Second Http Header content',
        ],
        [ 499, 599 ],
        [ 499, 599 ],
        true,
        true,
        true,
        'PDF/A-1a',
        true,
        [ 'Producer' => 'Gotenberg' ],
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

it(
    'creates a valid request for the "/forms/chromium/convert/markdown" endpoint',
    /**
     * @param ChromiumCookie[] $cookies
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param int[] $failOnResourceHttpStatusCodes
     * @param Stream[] $markdowns
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[] $assets
     */
    function (
        Stream $index,
        array $markdowns,
        bool $singlePage = false,
        float|string|null $paperWidth = null,
        float|string $paperHeight = 0,
        float|string|null $marginTop = null,
        float|string $marginBottom = 0,
        float|string $marginLeft = 0,
        float|string $marginRight = 0,
        bool $preferCssPageSize = false,
        bool $generateDocumentOutline = false,
        bool $printBackground = false,
        bool $omitBackground = false,
        bool $landscape = false,
        float|null $scale = null,
        string|null $nativePageRanges = null,
        Stream|null $header = null,
        Stream|null $footer = null,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $emulatedMediaType = null,
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->pdf();
        $chromium = hydrateChromiumPdfFormData(
            $chromium,
            $singlePage,
            $paperWidth,
            $paperHeight,
            $marginTop,
            $marginBottom,
            $marginLeft,
            $marginRight,
            $preferCssPageSize,
            $generateDocumentOutline,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
            $metadata,
            $assets,
        );

        $request = $chromium->markdown($index, ...$markdowns);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/chromium/convert/markdown');

        $index->getStream()->rewind();
        expect($body)->toContainFormFile('index.html', $index->getStream()->getContents(), 'text/html');

        foreach ($markdowns as $markdown) {
            $markdown->getStream()->rewind();
            expect($body)->toContainFormFile($markdown->getFilename(), $markdown->getStream()->getContents());
        }

        expectChromiumPdfOptions(
            $body,
            $singlePage,
            $paperWidth,
            $paperHeight,
            $marginTop,
            $marginBottom,
            $marginLeft,
            $marginRight,
            $preferCssPageSize,
            $generateDocumentOutline,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
            $metadata,
            $assets,
        );
    },
)->with([
    [
        Stream::string('my.html', 'HTML content'),
        [
            Stream::string('my.md', 'Markdown content'),
        ],
    ],
    [
        Stream::string('my.html', 'HTML content'),
        [
            Stream::string('my.md', 'Markdown content'),
            Stream::string('my_second.md', 'Second Markdown content'),
        ],
        true,
        8.27,
        11.7,
        '192px',
        2,
        2,
        2,
        true,
        true,
        true,
        true,
        true,
        1.0,
        '1-2',
        Stream::string('my_header.html', 'Header content'),
        Stream::string('my_footer.html', 'Footer content'),
        '1s',
        "window.status === 'ready'",
        'screen',
        [
            new ChromiumCookie('yummy_cookie', 'choco', 'theyummycookie.com'),
            new ChromiumCookie('vanilla_cookie', 'vanilla', 'theyummycookie.com', '/', true, true, 'Lax'),
        ],
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko)',
        [
            'My-Http-Header' => 'Http Header content',
            'My-Second-Http-Header' => 'Second Http Header content',
        ],
        [ 499, 599 ],
        [ 499, 599 ],
        true,
        true,
        true,
        'PDF/A-1a',
        true,
        [ 'Producer' => 'Gotenberg' ],
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

/**
 * @param ChromiumCookie[]                                  $cookies
 * @param array<string,string>                              $extraHttpHeaders
 * @param int[]                                             $failOnHttpStatusCodes
 * @param int[]                                             $failOnResourceHttpStatusCodes
 * @param array<string,string|bool|float|int|array<string>> $metadata
 * @param Stream[]                                          $assets
 */
function hydrateChromiumPdfFormData(
    ChromiumPdf $chromium,
    bool $singlePage = false,
    float|string|null $paperWidth = null,
    float|string $paperHeight = 0,
    float|string|null $marginTop = null,
    float|string $marginBottom = 0,
    float|string $marginLeft = 0,
    float|string $marginRight = 0,
    bool $preferCssPageSize = false,
    bool $generateDocumentOutline = false,
    bool $printBackground = false,
    bool $omitBackground = false,
    bool $landscape = false,
    float|null $scale = null,
    string|null $nativePageRanges = null,
    Stream|null $header = null,
    Stream|null $footer = null,
    string|null $waitDelay = null,
    string|null $waitForExpression = null,
    string|null $emulatedMediaType = null,
    array $cookies = [],
    string|null $userAgent = null,
    array $extraHttpHeaders = [],
    array $failOnHttpStatusCodes = [],
    array $failOnResourceHttpStatusCodes = [],
    bool $failOnResourceLoadingFailed = false,
    bool $failOnConsoleExceptions = false,
    bool|null $skipNetworkIdleEvent = null,
    string|null $pdfa = null,
    bool $pdfua = false,
    array $metadata = [],
    array $assets = [],
): ChromiumPdf {
    if ($singlePage) {
        $chromium->singlePage();
    }

    if ($paperWidth !== null) {
        $chromium->paperSize($paperWidth, $paperHeight);
    }

    if ($marginTop !== null) {
        $chromium->margins($marginTop, $marginBottom, $marginLeft, $marginRight);
    }

    if ($preferCssPageSize) {
        $chromium->preferCssPageSize();
    }

    if ($generateDocumentOutline) {
        $chromium->generateDocumentOutline();
    }

    if ($printBackground) {
        $chromium->printBackground();
    }

    if ($omitBackground) {
        $chromium->omitBackground();
    }

    if ($landscape) {
        $chromium->landscape();
    }

    if ($scale !== null) {
        $chromium->scale($scale);
    }

    if ($nativePageRanges !== null) {
        $chromium->nativePageRanges($nativePageRanges);
    }

    if ($header !== null) {
        $chromium->header($header);
    }

    if ($footer !== null) {
        $chromium->footer($footer);
    }

    if ($waitDelay !== null) {
        $chromium->waitDelay($waitDelay);
    }

    if ($waitForExpression !== null) {
        $chromium->waitForExpression($waitForExpression);
    }

    if ($emulatedMediaType === 'print') {
        $chromium->emulatePrintMediaType();
    }

    if ($emulatedMediaType === 'screen') {
        $chromium->emulateScreenMediaType();
    }

    if (count($cookies) > 0) {
        $chromium->cookies($cookies);
    }

    if ($userAgent !== null) {
        $chromium->userAgent($userAgent);
    }

    if (count($extraHttpHeaders) > 0) {
        $chromium->extraHttpHeaders($extraHttpHeaders);
    }

    if (count($failOnHttpStatusCodes) > 0) {
        $chromium->failOnHttpStatusCodes($failOnHttpStatusCodes);
    }

    if (count($failOnResourceHttpStatusCodes) > 0) {
        $chromium->failOnResourceHttpStatusCodes($failOnResourceHttpStatusCodes);
    }

    if ($failOnResourceLoadingFailed) {
        $chromium->failOnResourceLoadingFailed();
    }

    if ($failOnConsoleExceptions) {
        $chromium->failOnConsoleExceptions();
    }

    if ($skipNetworkIdleEvent !== null) {
        $chromium->skipNetworkIdleEvent($skipNetworkIdleEvent);
    }

    if ($pdfa !== null) {
        $chromium->pdfa($pdfa);
    }

    if ($pdfua) {
        $chromium->pdfua();
    }

    if (count($metadata) > 0) {
        $chromium->metadata($metadata);
    }

    if (count($assets) > 0) {
        $chromium->assets(...$assets);
    }

    return $chromium;
}

/**
 * @param ChromiumCookie[]                                  $cookies
 * @param array<string,string>                              $extraHttpHeaders
 * @param int[]                                             $failOnHttpStatusCodes
 * @param int[]                                             $failOnResourceHttpStatusCodes
 * @param array<string,string|bool|float|int|array<string>> $metadata
 * @param Stream[]                                          $assets
 */
function expectChromiumPdfOptions(
    string $body,
    bool $singlePage,
    float|string|null $paperWidth,
    float|string $paperHeight,
    float|string|null $marginTop,
    float|string $marginBottom,
    float|string $marginLeft,
    float|string $marginRight,
    bool $preferCssPageSize,
    bool $generateDocumentOutline,
    bool $printBackground,
    bool $omitBackground,
    bool $landscape,
    float|null $scale,
    string|null $nativePageRanges,
    Stream|null $header,
    Stream|null $footer,
    string|null $waitDelay,
    string|null $waitForExpression,
    string|null $emulatedMediaType,
    array $cookies,
    string|null $userAgent,
    array $extraHttpHeaders,
    array $failOnHttpStatusCodes,
    array $failOnResourceHttpStatusCodes,
    bool $failOnResourceLoadingFailed,
    bool $failOnConsoleExceptions,
    bool|null $skipNetworkIdleEvent,
    string|null $pdfa,
    bool $pdfua,
    array $metadata,
    array $assets,
): void {
    expect($body)->unless($singlePage === false, fn ($body) => $body->toContainFormValue('singlePage', '1'));

    if ($paperWidth !== null) {
        expect($body)
            ->toContainFormValue('paperWidth', $paperWidth . '')
            ->toContainFormValue('paperHeight', $paperHeight . '');
    }

    if ($marginTop !== null) {
        expect($body)
            ->toContainFormValue('marginTop', $marginTop . '')
            ->toContainFormValue('marginBottom', $marginBottom . '')
            ->toContainFormValue('marginLeft', $marginLeft . '')
            ->toContainFormValue('marginRight', $marginRight . '');
    }

    expect($body)->unless($preferCssPageSize === false, fn ($body) => $body->toContainFormValue('preferCssPageSize', '1'));
    expect($body)->unless($generateDocumentOutline === false, fn ($body) => $body->toContainFormValue('generateDocumentOutline', '1'));
    expect($body)->unless($printBackground === false, fn ($body) => $body->toContainFormValue('printBackground', '1'));
    expect($body)->unless($omitBackground === false, fn ($body) => $body->toContainFormValue('omitBackground', '1'));
    expect($body)->unless($landscape === false, fn ($body) => $body->toContainFormValue('landscape', '1'));
    expect($body)->unless($scale === null, fn ($body) => $body->toContainFormValue('scale', $scale . ''));
    expect($body)->unless($nativePageRanges === null, fn ($body) => $body->toContainFormValue('nativePageRanges', $nativePageRanges));

    if ($header !== null) {
        $header->getStream()->rewind();
        expect($body)->toContainFormFile('header.html', $header->getStream()->getContents(), 'text/html');
    }

    if ($footer !== null) {
        $footer->getStream()->rewind();
        expect($body)->toContainFormFile('footer.html', $footer->getStream()->getContents(), 'text/html');
    }

    expect($body)->unless($waitDelay === null, fn ($body) => $body->toContainFormValue('waitDelay', $waitDelay));
    expect($body)->unless($waitForExpression === null, fn ($body) => $body->toContainFormValue('waitForExpression', $waitForExpression));
    expect($body)->unless($emulatedMediaType === null, fn ($body) => $body->toContainFormValue('emulatedMediaType', $emulatedMediaType));

    if (count($cookies) > 0) {
        $json = json_encode($cookies);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        expect($body)->toContainFormValue('cookies', $json);
    }

    expect($body)->unless($userAgent === null, fn ($body) => $body->toContainFormValue('userAgent', $userAgent));

    if (count($extraHttpHeaders) > 0) {
        $json = json_encode($extraHttpHeaders);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        expect($body)->toContainFormValue('extraHttpHeaders', $json);
    }

    if (count($failOnHttpStatusCodes) > 0) {
        $json = json_encode($failOnHttpStatusCodes);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        expect($body)->toContainFormValue('failOnHttpStatusCodes', $json);
    }

    if (count($failOnResourceHttpStatusCodes) > 0) {
        $json = json_encode($failOnResourceHttpStatusCodes);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        expect($body)->toContainFormValue('failOnResourceHttpStatusCodes', $json);
    }

    expect($body)->unless($failOnResourceLoadingFailed === false, fn ($body) => $body->toContainFormValue('failOnResourceLoadingFailed', '1'));
    expect($body)->unless($failOnConsoleExceptions === false, fn ($body) => $body->toContainFormValue('failOnConsoleExceptions', '1'));
    expect($body)->unless($skipNetworkIdleEvent === null, fn ($body) => $body->toContainFormValue('skipNetworkIdleEvent', $skipNetworkIdleEvent === true ? '1' : '0'));
    expect($body)->unless($pdfa === null, fn ($body) => $body->toContainFormValue('pdfa', $pdfa));
    expect($body)->unless($pdfua === false, fn ($body) => $body->toContainFormValue('pdfua', '1'));

    if (count($metadata) > 0) {
        $json = json_encode($metadata);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        expect($body)->toContainFormValue('metadata', $json);
    }

    if (count($assets) <= 0) {
        return;
    }

    foreach ($assets as $asset) {
        $asset->getStream()->rewind();
        expect($body)->toContainFormFile($asset->getFilename(), $asset->getStream()->getContents(), 'image/jpeg');
    }
}
