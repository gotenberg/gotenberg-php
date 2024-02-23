<?php

declare(strict_types=1);

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Gotenberg;
use Gotenberg\Modules\ChromiumPdf;
use Gotenberg\Stream;

it(
    'creates a valid request for the "/forms/chromium/convert/url" endpoint',
    /**
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param Stream[] $assets
     */
    function (
        string $url,
        bool $singlePage = false,
        float|null $paperWidth = null,
        float $paperHeight = 0,
        float|null $marginTop = null,
        float $marginBottom = 0,
        float $marginLeft = 0,
        float $marginRight = 0,
        bool $preferCssPageSize = false,
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
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        bool $failOnConsoleExceptions = false,
        bool $skipNetworkIdleEvent = false,
        string|null $pdfa = null,
        bool $pdfua = false,
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
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
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
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
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
        2,
        2,
        2,
        2,
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
            'My-Http-Header' => 'HTTP Header content',
            'My-Second-Http-Header' => 'Second HTTP Header content',
        ],
        [ 499, 599 ],
        true,
        true,
        'PDF/A-1a',
        true,
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

it(
    'creates a valid request for the "/forms/chromium/convert/html" endpoint',
    /**
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param Stream[] $assets
     */
    function (
        Stream $index,
        bool $singlePage = false,
        float|null $paperWidth = null,
        float $paperHeight = 0,
        float|null $marginTop = null,
        float $marginBottom = 0,
        float $marginLeft = 0,
        float $marginRight = 0,
        bool $preferCssPageSize = false,
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
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        bool $failOnConsoleExceptions = false,
        bool $skipNetworkIdleEvent = false,
        string|null $pdfa = null,
        bool $pdfua = false,
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
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
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
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
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
        2,
        2,
        2,
        2,
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
            'My-Http-Header' => 'Http Header content',
            'My-Second-Http-Header' => 'Second Http Header content',
        ],
        [ 499, 599 ],
        true,
        true,
        'PDF/A-1a',
        true,
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

it(
    'creates a valid request for the "/forms/chromium/convert/markdown" endpoint',
    /**
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param Stream[] $markdowns
     * @param Stream[] $assets
     */
    function (
        Stream $index,
        array $markdowns,
        bool $singlePage = false,
        float|null $paperWidth = null,
        float $paperHeight = 0,
        float|null $marginTop = null,
        float $marginBottom = 0,
        float $marginLeft = 0,
        float $marginRight = 0,
        bool $preferCssPageSize = false,
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
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        bool $failOnConsoleExceptions = false,
        bool $skipNetworkIdleEvent = false,
        string|null $pdfa = null,
        bool $pdfua = false,
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
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
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
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $pdfa,
            $pdfua,
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
        2,
        2,
        2,
        2,
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
            'My-Http-Header' => 'Http Header content',
            'My-Second-Http-Header' => 'Second Http Header content',
        ],
        [ 499, 599 ],
        true,
        true,
        'PDF/A-1a',
        true,
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

/**
 * @param array<string,string> $extraHttpHeaders
 * @param int[]                $failOnHttpStatusCodes
 * @param Stream[]             $assets
 */
function hydrateChromiumPdfFormData(
    ChromiumPdf $chromium,
    bool $singlePage = false,
    float|null $paperWidth = null,
    float $paperHeight = 0,
    float|null $marginTop = null,
    float $marginBottom = 0,
    float $marginLeft = 0,
    float $marginRight = 0,
    bool $preferCssPageSize = false,
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
    array $extraHttpHeaders = [],
    array $failOnHttpStatusCodes = [],
    bool $failOnConsoleExceptions = false,
    bool $skipNetworkIdleEvent = false,
    string|null $pdfa = null,
    bool $pdfua = false,
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

    if (count($extraHttpHeaders) > 0) {
        $chromium->extraHttpHeaders($extraHttpHeaders);
    }

    if (count($failOnHttpStatusCodes) > 0) {
        $chromium->failOnHttpStatusCodes($failOnHttpStatusCodes);
    }

    if ($failOnConsoleExceptions) {
        $chromium->failOnConsoleExceptions();
    }

    if ($skipNetworkIdleEvent) {
        $chromium->skipNetworkIdleEvent();
    }

    if ($pdfa !== null) {
        $chromium->pdfa($pdfa);
    }

    if ($pdfua) {
        $chromium->pdfua();
    }

    if (count($assets) > 0) {
        $chromium->assets(...$assets);
    }

    return $chromium;
}

/**
 * @param array<string,string> $extraHttpHeaders
 * @param int[]                $failOnHttpStatusCodes
 * @param Stream[]             $assets
 */
function expectChromiumPdfOptions(
    string $body,
    bool $singlePage,
    float|null $paperWidth,
    float $paperHeight,
    float|null $marginTop,
    float $marginBottom,
    float $marginLeft,
    float $marginRight,
    bool $preferCssPageSize,
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
    array $extraHttpHeaders,
    array $failOnHttpStatusCodes,
    bool $failOnConsoleExceptions,
    bool $skipNetworkIdleEvent,
    string|null $pdfa,
    bool $pdfua,
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

    expect($body)->unless($failOnConsoleExceptions === false, fn ($body) => $body->toContainFormValue('failOnConsoleExceptions', '1'));
    expect($body)->unless($skipNetworkIdleEvent === false, fn ($body) => $body->toContainFormValue('skipNetworkIdleEvent', '1'));
    expect($body)->unless($pdfa === null, fn ($body) => $body->toContainFormValue('pdfa', $pdfa));
    expect($body)->unless($pdfua === false, fn ($body) => $body->toContainFormValue('pdfua', '1'));

    if (count($assets) <= 0) {
        return;
    }

    foreach ($assets as $asset) {
        $asset->getStream()->rewind();
        expect($body)->toContainFormFile($asset->getFilename(), $asset->getStream()->getContents(), 'image/jpeg');
    }
}
