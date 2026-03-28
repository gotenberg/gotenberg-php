<?php

declare(strict_types=1);

namespace Gotenberg\Test\Modules;

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Gotenberg;
use Gotenberg\Modules\ChromiumCookie;
use Gotenberg\Modules\ChromiumEmulatedMediaFeatures;
use Gotenberg\Modules\ChromiumScreenshot;
use Gotenberg\Stream;
use Gotenberg\Test\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use function count;
use function json_encode;

final class ChromiumScreenshotTest extends TestCase
{
    /**
     * @param ChromiumEmulatedMediaFeatures[] $emulatedMediaFeatures
     * @param ChromiumCookie[]                $cookies
     * @param array<string,string>            $extraHttpHeaders
     * @param int[]                           $failOnHttpStatusCodes
     * @param int[]                           $failOnResourceHttpStatusCodes
     * @param string[]                        $ignoreResourceHttpStatusDomains
     * @param Stream[]                        $assets
     */
    #[Test]
    #[DataProvider('provideUrlData')]
    public function it_creates_a_valid_request_for_the_forms_chromium_screenshot_url_endpoint(
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
        string|null $waitForSelector = null,
        string|null $emulatedMediaType = null,
        array $emulatedMediaFeatures = [],
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        array $ignoreResourceHttpStatusDomains = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        bool|null $skipNetworkAlmostIdleEvent = null,
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->screenshot();
        $chromium = $this->hydrateChromiumScreenshotFormData(
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
            $waitForSelector,
            $emulatedMediaType,
            $emulatedMediaFeatures,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $skipNetworkAlmostIdleEvent,
            $assets,
        );

        $request = $chromium->url($url);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/chromium/screenshot/url', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'url', $url);

        $this->assertChromiumScreenshotOptions(
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
            $waitForSelector,
            $emulatedMediaType,
            $emulatedMediaFeatures,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $skipNetworkAlmostIdleEvent,
            $assets,
        );
    }

    /**
     * @return array<string, array{
     * string,
     * int|null,
     * int|null,
     * bool,
     * string|null,
     * int|null,
     * bool,
     * bool,
     * string|null,
     * string|null,
     * string|null,
     * string|null,
     * array<int, ChromiumEmulatedMediaFeatures>,
     * array<int, ChromiumCookie>,
     * string|null,
     * array<string, string>,
     * array<int, int>,
     * array<int, int>,
     * array<int, string>,
     * bool,
     * bool,
     * bool|null,
     * bool|null,
     * array<int, Stream>
     * }>
     */
    public static function provideUrlData(): array
    {
        return [
            'simple_url' => ['https://my.url'],
            'full_options' => [
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
                '#id',
                'print',
                [
                    new ChromiumEmulatedMediaFeatures('prefers-color-scheme', 'dark'),
                    new ChromiumEmulatedMediaFeatures('prefers-reduced-motion', 'reduce'),
                ],
                [
                    new ChromiumCookie('yummy_cookie', 'choco', 'theyummycookie.com'),
                    new ChromiumCookie('vanilla_cookie', 'vanilla', 'theyummycookie.com', '/', true, true, 'Lax'),
                ],
                'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko)',
                [
                    'My-Http-Header' => 'HTTP Header content',
                    'My-Second-Http-Header' => 'Second HTTP Header content',
                ],
                [499, 599],
                [499, 599],
                ['my.com'],
                true,
                true,
                true,
                true,
                [
                    Stream::string('my.jpg', 'Image content'),
                ],
            ],
        ];
    }

    /**
     * @param ChromiumEmulatedMediaFeatures[] $emulatedMediaFeatures
     * @param ChromiumCookie[]                $cookies
     * @param array<string,string>            $extraHttpHeaders
     * @param int[]                           $failOnHttpStatusCodes
     * @param int[]                           $failOnResourceHttpStatusCodes
     * @param string[]                        $ignoreResourceHttpStatusDomains
     * @param Stream[]                        $assets
     */
    #[Test]
    #[DataProvider('provideHtmlData')]
    public function it_creates_a_valid_request_for_the_forms_chromium_screenshot_html_endpoint(
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
        string|null $waitForSelector = null,
        string|null $emulatedMediaType = null,
        array $emulatedMediaFeatures = [],
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        array $ignoreResourceHttpStatusDomains = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        bool|null $skipNetworkAlmostIdleEvent = null,
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->screenshot();
        $chromium = $this->hydrateChromiumScreenshotFormData(
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
            $waitForSelector,
            $emulatedMediaType,
            $emulatedMediaFeatures,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $skipNetworkAlmostIdleEvent,
            $assets,
        );

        $request = $chromium->html($index);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/chromium/screenshot/html', $request->getUri()->getPath());

        $index->getStream()->rewind();
        $this->assertContainsFormFile($body, 'index.html', $index->getStream()->getContents(), 'text/html');

        $this->assertChromiumScreenshotOptions(
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
            $waitForSelector,
            $emulatedMediaType,
            $emulatedMediaFeatures,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $skipNetworkAlmostIdleEvent,
            $assets,
        );
    }

    /**
     * @return array<string, array{
     * Stream,
     * int|null,
     * int|null,
     * bool,
     * string|null,
     * int|null,
     * bool,
     * bool,
     * string|null,
     * string|null,
     * string|null,
     * string|null,
     * array<int, ChromiumEmulatedMediaFeatures>,
     * array<int, ChromiumCookie>,
     * string|null,
     * array<string, string>,
     * array<int, int>,
     * array<int, int>,
     * array<int, string>,
     * bool,
     * bool,
     * bool|null,
     * bool|null,
     * array<int, Stream>
     * }>
     */
    public static function provideHtmlData(): array
    {
        return [
            'simple_html' => [Stream::string('my.html', 'HTML content')],
            'full_options' => [
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
                '#id',
                'screen',
                [
                    new ChromiumEmulatedMediaFeatures('prefers-color-scheme', 'light'),
                    new ChromiumEmulatedMediaFeatures('forced-colors', 'none'),
                ],
                [
                    new ChromiumCookie('yummy_cookie', 'choco', 'theyummycookie.com'),
                    new ChromiumCookie('vanilla_cookie', 'vanilla', 'theyummycookie.com', '/', true, true, 'Lax'),
                ],
                'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko)',
                [
                    'My-Http-Header' => 'Http Header content',
                    'My-Second-Http-Header' => 'Second Http Header content',
                ],
                [499, 599],
                [499, 599],
                ['my.com'],
                true,
                true,
                true,
                true,
                [
                    Stream::string('my.jpg', 'Image content'),
                ],
            ],
        ];
    }

    /**
     * @param ChromiumEmulatedMediaFeatures[] $emulatedMediaFeatures
     * @param ChromiumCookie[]                $cookies
     * @param array<string,string>            $extraHttpHeaders
     * @param int[]                           $failOnHttpStatusCodes
     * @param int[]                           $failOnResourceHttpStatusCodes
     * @param string[]                        $ignoreResourceHttpStatusDomains,
     * @param Stream[]                        $markdowns
     * @param Stream[]                        $assets
     */
    #[Test]
    #[DataProvider('provideMarkdownData')]
    public function it_creates_a_valid_request_for_the_forms_chromium_screenshot_markdown_endpoint(
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
        string|null $waitForSelector = null,
        string|null $emulatedMediaType = null,
        array $emulatedMediaFeatures = [],
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        array $ignoreResourceHttpStatusDomains = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        bool|null $skipNetworkAlmostIdleEvent = null,
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->screenshot();
        $chromium = $this->hydrateChromiumScreenshotFormData(
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
            $waitForSelector,
            $emulatedMediaType,
            $emulatedMediaFeatures,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $skipNetworkAlmostIdleEvent,
            $assets,
        );

        $request = $chromium->markdown($index, ...$markdowns);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/chromium/screenshot/markdown', $request->getUri()->getPath());

        $index->getStream()->rewind();
        $this->assertContainsFormFile($body, 'index.html', $index->getStream()->getContents(), 'text/html');

        foreach ($markdowns as $markdown) {
            $markdown->getStream()->rewind();
            $this->assertContainsFormFile($body, $markdown->getFilename(), $markdown->getStream()->getContents());
        }

        $this->assertChromiumScreenshotOptions(
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
            $waitForSelector,
            $emulatedMediaType,
            $emulatedMediaFeatures,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $skipNetworkAlmostIdleEvent,
            $assets,
        );
    }

    /**
     * @return array<string, array{
     * Stream,
     * array<int, Stream>,
     * int|null,
     * int|null,
     * bool,
     * string|null,
     * int|null,
     * bool,
     * bool,
     * string|null,
     * string|null,
     * string|null,
     * string|null,
     * array<int, ChromiumEmulatedMediaFeatures>,
     * array<int, ChromiumCookie>,
     * string|null,
     * array<string, string>,
     * array<int, int>,
     * array<int, int>,
     * array<int, string>,
     * bool,
     * bool,
     * bool|null,
     * bool|null,
     * array<int, Stream>
     * }>
     */
    public static function provideMarkdownData(): array
    {
        return [
            'simple_markdown' => [
                Stream::string('my.html', 'HTML content'),
                [
                    Stream::string('my.md', 'Markdown content'),
                ],
            ],
            'full_options' => [
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
                '#id',
                'screen',
                [
                    new ChromiumEmulatedMediaFeatures('prefers-reduced-motion', 'reduce'),
                ],
                [
                    new ChromiumCookie('yummy_cookie', 'choco', 'theyummycookie.com'),
                    new ChromiumCookie('vanilla_cookie', 'vanilla', 'theyummycookie.com', '/', true, true, 'Lax'),
                ],
                'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko)',
                [
                    'My-Http-Header' => 'Http Header content',
                    'My-Second-Http-Header' => 'Second Http Header content',
                ],
                [499, 599],
                [499, 599],
                ['my.com'],
                true,
                true,
                true,
                true,
                [
                    Stream::string('my.jpg', 'Image content'),
                ],
            ],
        ];
    }

    /**
     * @param ChromiumEmulatedMediaFeatures[] $emulatedMediaFeatures
     * @param ChromiumCookie[]                $cookies
     * @param array<string,string>            $extraHttpHeaders
     * @param int[]                           $failOnHttpStatusCodes
     * @param int[]                           $failOnResourceHttpStatusCodes
     * @param string[]                        $ignoreResourceHttpStatusDomains
     * @param Stream[]                        $assets
     */
    private function hydrateChromiumScreenshotFormData(
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
        string|null $waitForSelector = null,
        string|null $emulatedMediaType = null,
        array $emulatedMediaFeatures = [],
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        array $ignoreResourceHttpStatusDomains = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        bool|null $skipNetworkAlmostIdleEvent = null,
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

        if ($waitForSelector !== null) {
            $chromium->waitForSelector($waitForSelector);
        }

        if ($emulatedMediaType === 'print') {
            $chromium->emulatePrintMediaType();
        }

        if ($emulatedMediaType === 'screen') {
            $chromium->emulateScreenMediaType();
        }

        if (count($emulatedMediaFeatures) > 0) {
            $chromium->emulatedMediaFeatures($emulatedMediaFeatures);
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

        if (count($ignoreResourceHttpStatusDomains) > 0) {
            $chromium->ignoreResourceHttpStatusDomains($ignoreResourceHttpStatusDomains);
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

        if ($skipNetworkAlmostIdleEvent !== null) {
            $chromium->skipNetworkAlmostIdleEvent($skipNetworkAlmostIdleEvent);
        }

        if (count($assets) > 0) {
            $chromium->assets(...$assets);
        }

        return $chromium;
    }

    /**
     * @param ChromiumEmulatedMediaFeatures[] $emulatedMediaFeatures
     * @param ChromiumCookie[]                $cookies
     * @param array<string,string>            $extraHttpHeaders
     * @param int[]                           $failOnHttpStatusCodes
     * @param int[]                           $failOnResourceHttpStatusCodes
     * @param string[]                        $ignoreResourceHttpStatusDomains
     * @param Stream[]                        $assets
     */
    private function assertChromiumScreenshotOptions(
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
        string|null $waitForSelector,
        string|null $emulatedMediaType,
        array $emulatedMediaFeatures,
        array $cookies,
        string|null $userAgent,
        array $extraHttpHeaders,
        array $failOnHttpStatusCodes,
        array $failOnResourceHttpStatusCodes,
        array $ignoreResourceHttpStatusDomains,
        bool $failOnResourceLoadingFailed,
        bool $failOnConsoleExceptions,
        bool|null $skipNetworkIdleEvent,
        bool|null $skipNetworkAlmostIdleEvent,
        array $assets,
    ): void {
        if ($width !== null) {
            $this->assertContainsFormValue($body, 'width', (string) $width);
        }

        if ($height !== null) {
            $this->assertContainsFormValue($body, 'height', (string) $height);
        }

        if ($clip) {
            $this->assertContainsFormValue($body, 'clip', '1');
        }

        if ($format !== null) {
            $this->assertContainsFormValue($body, 'format', $format);
        }

        if ($quality !== null) {
            $this->assertContainsFormValue($body, 'quality', (string) $quality);
        }

        if ($optimizeForSpeed) {
            $this->assertContainsFormValue($body, 'optimizeForSpeed', '1');
        }

        if ($omitBackground) {
            $this->assertContainsFormValue($body, 'omitBackground', '1');
        }

        if ($waitDelay !== null) {
            $this->assertContainsFormValue($body, 'waitDelay', $waitDelay);
        }

        if ($waitForExpression !== null) {
            $this->assertContainsFormValue($body, 'waitForExpression', $waitForExpression);
        }

        if ($waitForSelector !== null) {
            $this->assertContainsFormValue($body, 'waitForSelector', $waitForSelector);
        }

        if ($emulatedMediaType !== null) {
            $this->assertContainsFormValue($body, 'emulatedMediaType', $emulatedMediaType);
        }

        if (count($emulatedMediaFeatures) > 0) {
            $json = json_encode($emulatedMediaFeatures);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'emulatedMediaFeatures', $json);
        }

        if (count($cookies) > 0) {
            $json = json_encode($cookies);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'cookies', $json);
        }

        if ($userAgent !== null) {
            $this->assertContainsFormValue($body, 'userAgent', $userAgent);
        }

        if (count($extraHttpHeaders) > 0) {
            $json = json_encode($extraHttpHeaders);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'extraHttpHeaders', $json);
        }

        if (count($failOnHttpStatusCodes) > 0) {
            $json = json_encode($failOnHttpStatusCodes);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'failOnHttpStatusCodes', $json);
        }

        if (count($failOnResourceHttpStatusCodes) > 0) {
            $json = json_encode($failOnResourceHttpStatusCodes);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'failOnResourceHttpStatusCodes', $json);
        }

        if (count($ignoreResourceHttpStatusDomains) > 0) {
            $json = json_encode($ignoreResourceHttpStatusDomains);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'ignoreResourceHttpStatusDomains', $json);
        }

        if ($failOnResourceLoadingFailed) {
            $this->assertContainsFormValue($body, 'failOnResourceLoadingFailed', '1');
        }

        if ($failOnConsoleExceptions) {
            $this->assertContainsFormValue($body, 'failOnConsoleExceptions', '1');
        }

        if ($skipNetworkIdleEvent !== null) {
            $this->assertContainsFormValue($body, 'skipNetworkIdleEvent', $skipNetworkIdleEvent ? '1' : '0');
        }

        if ($skipNetworkAlmostIdleEvent !== null) {
            $this->assertContainsFormValue($body, 'skipNetworkAlmostIdleEvent', $skipNetworkAlmostIdleEvent ? '1' : '0');
        }

        if (count($assets) <= 0) {
            return;
        }

        foreach ($assets as $asset) {
            $asset->getStream()->rewind();
            $this->assertContainsFormFile($body, $asset->getFilename(), $asset->getStream()->getContents(), 'image/jpeg');
        }
    }
}
