<?php

declare(strict_types=1);

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Gotenberg;
use Gotenberg\Modules\ChromiumScreenshot;
use Gotenberg\Stream;

it(
    'creates a valid request for the "/forms/chromium/screenshot/url" endpoint',
    /**
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param Stream[] $assets
     */
    function (
        string $url,
        string|null $format = null,
        int|null $quality = null,
        bool $optimizeForSpeed = false,
        bool $omitBackground = false,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $emulatedMediaType = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        bool $failOnConsoleExceptions = false,
        bool $skipNetworkIdleEvent = false,
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->screenshot();
        $chromium = hydrateChromiumScreenshotFormData(
            $chromium,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $assets,
        );

        $request = $chromium->url($url);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/chromium/screenshot/url');
        expect($body)->toContainFormValue('url', $url);

        expectChromiumScreenshotOptions(
            $body,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $assets,
        );
    },
)->with([
    ['https://my.url'],
    ['https://my.url'],
    [
        'https://my.url',
        'png',
        100,
        true,
        true,
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
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

it(
    'creates a valid request for the "/forms/chromium/screenshot/html" endpoint',
    /**
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param Stream[] $assets
     */
    function (
        Stream $index,
        string|null $format = null,
        int|null $quality = null,
        bool $optimizeForSpeed = false,
        bool $omitBackground = false,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $emulatedMediaType = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        bool $failOnConsoleExceptions = false,
        bool $skipNetworkIdleEvent = false,
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->screenshot();
        $chromium = hydrateChromiumScreenshotFormData(
            $chromium,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $assets,
        );

        $request = $chromium->html($index);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/chromium/screenshot/html');

        $index->getStream()->rewind();
        expect($body)->toContainFormFile('index.html', $index->getStream()->getContents(), 'text/html');

        expectChromiumScreenshotOptions(
            $body,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $assets,
        );
    },
)->with([
    [Stream::string('my.html', 'HTML content')],
    [
        Stream::string('my.html', 'HTML content'),
        'jpeg',
        100,
        true,
        true,
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
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

it(
    'creates a valid request for the "/forms/chromium/screenshot/markdown" endpoint',
    /**
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param Stream[] $markdowns
     * @param Stream[] $assets
     */
    function (
        Stream $index,
        array $markdowns,
        string|null $format = null,
        int|null $quality = null,
        bool $optimizeForSpeed = false,
        bool $omitBackground = false,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $emulatedMediaType = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        bool $failOnConsoleExceptions = false,
        bool $skipNetworkIdleEvent = false,
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->screenshot();
        $chromium = hydrateChromiumScreenshotFormData(
            $chromium,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $assets,
        );

        $request = $chromium->markdown($index, ...$markdowns);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/chromium/screenshot/markdown');

        $index->getStream()->rewind();
        expect($body)->toContainFormFile('index.html', $index->getStream()->getContents(), 'text/html');

        foreach ($markdowns as $markdown) {
            $markdown->getStream()->rewind();
            expect($body)->toContainFormFile($markdown->getFilename(), $markdown->getStream()->getContents());
        }

        expectChromiumScreenshotOptions(
            $body,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
            $waitDelay,
            $waitForExpression,
            $emulatedMediaType,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
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
        'webp',
        100,
        true,
        true,
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
function hydrateChromiumScreenshotFormData(
    ChromiumScreenshot $chromium,
    string|null $format = null,
    int|null $quality = null,
    bool $optimizeForSpeed = false,
    bool $omitBackground = false,
    string|null $waitDelay = null,
    string|null $waitForExpression = null,
    string|null $emulatedMediaType = null,
    array $extraHttpHeaders = [],
    array $failOnHttpStatusCodes = [],
    bool $failOnConsoleExceptions = false,
    bool $skipNetworkIdleEvent = false,
    array $assets = [],
): ChromiumScreenshot {
    if ($format === 'png') {
        $chromium->png();
    }

    if ($format === 'jpeg') {
        $chromium->jpeg();
    }

    if ($format === 'webp') {
        $chromium->webp();
    }

    if ($quality !== null) {
        $chromium->quality($quality);
    }

    if ($optimizeForSpeed) {
        $chromium->optimizeForSpeed();
    }

    if ($omitBackground) {
        $chromium->omitBackground();
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
function expectChromiumScreenshotOptions(
    string $body,
    string|null $format,
    int|null $quality,
    bool $optimizeForSpeed,
    bool $omitBackground,
    string|null $waitDelay,
    string|null $waitForExpression,
    string|null $emulatedMediaType,
    array $extraHttpHeaders,
    array $failOnHttpStatusCodes,
    bool $failOnConsoleExceptions,
    bool $skipNetworkIdleEvent,
    array $assets,
): void {
    expect($body)->unless($format === null, fn ($body) => $body->toContainFormValue('format', $format));
    expect($body)->unless($quality === null, fn ($body) => $body->toContainFormValue('quality', $quality . ''));
    expect($body)->unless($optimizeForSpeed === false, fn ($body) => $body->toContainFormValue('optimizeForSpeed', '1'));
    expect($body)->unless($omitBackground === false, fn ($body) => $body->toContainFormValue('omitBackground', '1'));
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

    if (count($assets) <= 0) {
        return;
    }

    foreach ($assets as $asset) {
        $asset->getStream()->rewind();
        expect($body)->toContainFormFile($asset->getFilename(), $asset->getStream()->getContents(), 'image/jpeg');
    }
}
