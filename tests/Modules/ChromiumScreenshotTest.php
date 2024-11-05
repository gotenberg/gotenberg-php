<?php

declare(strict_types=1);

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Gotenberg;
use Gotenberg\Modules\ChromiumCookie;
use Gotenberg\Modules\ChromiumScreenshot;
use Gotenberg\Stream;

it(
    'creates a valid request for the "/forms/chromium/screenshot/url" endpoint',
    /**
     * @param ChromiumCookie[] $cookies
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param int[] $failOnResourceHttpStatusCodes
     * @param Stream[] $assets
     */
    function (
        string $url,
        int|null $width = null,
        int|null $height = null,
        bool $clip = false,
        string|null $format = null,
        int|null $quality = null,
        bool $optimizeForSpeed = false,
        bool $omitBackground = false,
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
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->screenshot();
        $chromium = hydrateChromiumScreenshotFormData(
            $chromium,
            $width,
            $height,
            $clip,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
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
            $assets,
        );

        $request = $chromium->url($url);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/chromium/screenshot/url');
        expect($body)->toContainFormValue('url', $url);

        expectChromiumScreenshotOptions(
            $body,
            $width,
            $height,
            $clip,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
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
            $assets,
        );
    },
)->with([
    ['https://my.url'],
    [
        'https://my.url',
        1280,
        800,
        true,
        'png',
        100,
        true,
        true,
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
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

it(
    'creates a valid request for the "/forms/chromium/screenshot/html" endpoint',
    /**
     * @param ChromiumCookie[] $cookies
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param int[] $failOnResourceHttpStatusCodes
     * @param Stream[] $assets
     */
    function (
        Stream $index,
        int|null $width = null,
        int|null $height = null,
        bool $clip = false,
        string|null $format = null,
        int|null $quality = null,
        bool $optimizeForSpeed = false,
        bool $omitBackground = false,
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
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->screenshot();
        $chromium = hydrateChromiumScreenshotFormData(
            $chromium,
            $width,
            $height,
            $clip,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
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
            $assets,
        );

        $request = $chromium->html($index);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/chromium/screenshot/html');

        $index->getStream()->rewind();
        expect($body)->toContainFormFile('index.html', $index->getStream()->getContents(), 'text/html');

        expectChromiumScreenshotOptions(
            $body,
            $width,
            $height,
            $clip,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
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
            $assets,
        );
    },
)->with([
    [Stream::string('my.html', 'HTML content')],
    [
        Stream::string('my.html', 'HTML content'),
        1280,
        800,
        true,
        'jpeg',
        100,
        true,
        true,
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
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

it(
    'creates a valid request for the "/forms/chromium/screenshot/markdown" endpoint',
    /**
     * @param ChromiumCookie[] $cookies
     * @param array<string,string> $extraHttpHeaders
     * @param int[] $failOnHttpStatusCodes
     * @param int[] $failOnResourceHttpStatusCodes
     * @param Stream[] $markdowns
     * @param Stream[] $assets
     */
    function (
        Stream $index,
        array $markdowns,
        int|null $width = null,
        int|null $height = null,
        bool $clip = false,
        string|null $format = null,
        int|null $quality = null,
        bool $optimizeForSpeed = false,
        bool $omitBackground = false,
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
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->screenshot();
        $chromium = hydrateChromiumScreenshotFormData(
            $chromium,
            $width,
            $height,
            $clip,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
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
            $width,
            $height,
            $clip,
            $format,
            $quality,
            $optimizeForSpeed,
            $omitBackground,
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
        1280,
        800,
        true,
        'webp',
        100,
        true,
        true,
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
        [
            Stream::string('my.jpg', 'Image content'),
        ],
    ],
]);

/**
 * @param ChromiumCookie[]     $cookies
 * @param array<string,string> $extraHttpHeaders
 * @param int[]                $failOnHttpStatusCodes
 * @param int[]                $failOnResourceHttpStatusCodes
 * @param Stream[]             $assets
 */
function hydrateChromiumScreenshotFormData(
    ChromiumScreenshot $chromium,
    int|null $width,
    int|null $height,
    bool $clip,
    string|null $format = null,
    int|null $quality = null,
    bool $optimizeForSpeed = false,
    bool $omitBackground = false,
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
    array $assets = [],
): ChromiumScreenshot {
    if ($width !== null) {
        $chromium->width($width);
    }

    if ($height !== null) {
        $chromium->height($height);
    }

    if ($clip) {
        $chromium->clip();
    }

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

    if (count($assets) > 0) {
        $chromium->assets(...$assets);
    }

    return $chromium;
}

/**
 * @param ChromiumCookie[]     $cookies
 * @param array<string,string> $extraHttpHeaders
 * @param int[]                $failOnHttpStatusCodes
 * @param int[]                $failOnResourceHttpStatusCodes
 * @param Stream[]             $assets
 */
function expectChromiumScreenshotOptions(
    string $body,
    int|null $width,
    int|null $height,
    bool $clip,
    string|null $format,
    int|null $quality,
    bool $optimizeForSpeed,
    bool $omitBackground,
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
    array $assets,
): void {
    expect($body)->unless($width === null, fn ($body) => $body->toContainFormValue('width', $width . ''));
    expect($body)->unless($height === null, fn ($body) => $body->toContainFormValue('height', $height . ''));
    expect($body)->unless($clip === false, fn ($body) => $body->toContainFormValue('clip', '1'));
    expect($body)->unless($format === null, fn ($body) => $body->toContainFormValue('format', $format));
    expect($body)->unless($quality === null, fn ($body) => $body->toContainFormValue('quality', $quality . ''));
    expect($body)->unless($optimizeForSpeed === false, fn ($body) => $body->toContainFormValue('optimizeForSpeed', '1'));
    expect($body)->unless($omitBackground === false, fn ($body) => $body->toContainFormValue('omitBackground', '1'));
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

    if (count($assets) <= 0) {
        return;
    }

    foreach ($assets as $asset) {
        $asset->getStream()->rewind();
        expect($body)->toContainFormFile($asset->getFilename(), $asset->getStream()->getContents(), 'image/jpeg');
    }
}
