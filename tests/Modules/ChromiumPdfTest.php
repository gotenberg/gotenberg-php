<?php

declare(strict_types=1);

namespace Gotenberg\Test\Modules;

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Gotenberg;
use Gotenberg\Modules\ChromiumCookie;
use Gotenberg\Modules\ChromiumPdf;
use Gotenberg\SplitMode;
use Gotenberg\Stream;
use Gotenberg\Test\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use function count;
use function json_encode;

final class ChromiumPdfTest extends TestCase
{
    /**
     * @param ChromiumCookie[]                                  $cookies
     * @param array<string,string>                              $extraHttpHeaders
     * @param int[]                                             $failOnHttpStatusCodes
     * @param int[]                                             $failOnResourceHttpStatusCodes
     * @param string[]                                          $ignoreResourceHttpStatusDomains
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $embeds
     * @param Stream[]                                          $assets
     */
    #[Test]
    #[DataProvider('provideUrlData')]
    public function it_creates_a_valid_request_for_the_forms_chromium_convert_url_endpoint(
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
        bool $generateTaggedPdf = false,
        bool $printBackground = false,
        bool $omitBackground = false,
        bool $landscape = false,
        float|null $scale = null,
        string|null $nativePageRanges = null,
        Stream|null $header = null,
        Stream|null $footer = null,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $waitForSelector = null,
        string|null $emulatedMediaType = null,
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        array $ignoreResourceHttpStatusDomains = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        SplitMode|null $splitMode = null,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        bool $flatten = false,
        string $userPassword = '',
        string $ownerPassword = '',
        array $embeds = [],
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->pdf();
        $chromium = $this->hydrateChromiumPdfFormData(
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
            $generateTaggedPdf,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $waitForSelector,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $splitMode,
            $pdfa,
            $pdfua,
            $metadata,
            $flatten,
            $userPassword,
            $ownerPassword,
            $embeds,
            $assets,
        );

        $request = $chromium->url($url);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/chromium/convert/url', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'url', $url);

        $this->assertChromiumPdfOptions(
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
            $generateTaggedPdf,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $waitForSelector,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $splitMode,
            $pdfa,
            $pdfua,
            $metadata,
            $flatten,
            $userPassword,
            $ownerPassword,
            $embeds,
            $assets,
        );
    }

    /**
     * @return array<string, array{
     * string,
     * bool,
     * float|string|null,
     * float|string,
     * float|string|null,
     * float|string,
     * float|string,
     * float|string,
     * bool,
     * bool,
     * bool,
     * bool,
     * bool,
     * bool,
     * float|null,
     * string|null,
     * Stream|null,
     * Stream|null,
     * string|null,
     * string|null,
     * string|null,
     * string|null,
     * array<int, ChromiumCookie>,
     * string|null,
     * array<string, string>,
     * array<int, int>,
     * array<int, int>,
     * array<int, string>,
     * bool,
     * bool,
     * bool|null,
     * SplitMode|null,
     * string|null,
     * bool,
     * array<string, string|bool|float|int|array<string>>,
     * bool,
     * string,
     * string,
     * array<int, Stream>,
     * array<int, Stream>
     * }>
     */
    public static function provideUrlData(): array
    {
        return [
            'simple_url' => ['https://my.url'],
            'full_options' => [
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
                true,
                1.0,
                '1-2',
                Stream::string('my_header.html', 'Header content'),
                Stream::string('my_footer.html', 'Footer content'),
                '1s',
                "window.status === 'ready'",
                '#id',
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
                [499, 599],
                [499, 599],
                ['my.com'],
                true,
                true,
                true,
                SplitMode::intervals(1),
                'PDF/A-1a',
                true,
                ['Producer' => 'Gotenberg'],
                true,
                'my_user_password',
                'my_owner_password',
                [
                    Stream::string('my.xml', 'XML content'),
                    Stream::string('my_second.xml', 'Second XML content'),
                ],
                [
                    Stream::string('my.jpg', 'Image content'),
                ],
            ],
        ];
    }

    /**
     * @param ChromiumCookie[]                                  $cookies
     * @param array<string,string>                              $extraHttpHeaders
     * @param int[]                                             $failOnHttpStatusCodes
     * @param int[]                                             $failOnResourceHttpStatusCodes
     * @param string[]                                          $ignoreResourceHttpStatusDomains
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $embeds
     * @param Stream[]                                          $assets
     */
    #[Test]
    #[DataProvider('provideHtmlData')]
    public function it_creates_a_valid_request_for_the_forms_chromium_convert_html_endpoint(
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
        bool $generateTaggedPdf = false,
        bool $printBackground = false,
        bool $omitBackground = false,
        bool $landscape = false,
        float|null $scale = null,
        string|null $nativePageRanges = null,
        Stream|null $header = null,
        Stream|null $footer = null,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $waitForSelector = null,
        string|null $emulatedMediaType = null,
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        array $ignoreResourceHttpStatusDomains = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        SplitMode|null $splitMode = null,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        bool $flatten = false,
        string $userPassword = '',
        string $ownerPassword = '',
        array $embeds = [],
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->pdf();
        $chromium = $this->hydrateChromiumPdfFormData(
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
            $generateTaggedPdf,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $waitForSelector,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $splitMode,
            $pdfa,
            $pdfua,
            $metadata,
            $flatten,
            $userPassword,
            $ownerPassword,
            $embeds,
            $assets,
        );

        $request = $chromium->html($index);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/chromium/convert/html', $request->getUri()->getPath());

        $index->getStream()->rewind();
        $this->assertContainsFormFile($body, 'index.html', $index->getStream()->getContents(), 'text/html');

        $this->assertChromiumPdfOptions(
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
            $generateTaggedPdf,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $waitForSelector,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $splitMode,
            $pdfa,
            $pdfua,
            $metadata,
            $flatten,
            $userPassword,
            $ownerPassword,
            $embeds,
            $assets,
        );
    }

    /**
     * @return array<string, array{
     * Stream,
     * bool,
     * float|string|null,
     * float|string,
     * float|string|null,
     * float|string,
     * float|string,
     * float|string,
     * bool,
     * bool,
     * bool,
     * bool,
     * bool,
     * bool,
     * float|null,
     * string|null,
     * Stream|null,
     * Stream|null,
     * string|null,
     * string|null,
     * string|null,
     * string|null,
     * array<int, ChromiumCookie>,
     * string|null,
     * array<string, string>,
     * array<int, int>,
     * array<int, int>,
     * array<int, string>,
     * bool,
     * bool,
     * bool|null,
     * SplitMode|null,
     * string|null,
     * bool,
     * array<string, string|bool|float|int|array<string>>,
     * bool,
     * string,
     * string,
     * array<int, Stream>,
     * array<int, Stream>
     * }>
     */
    public static function provideHtmlData(): array
    {
        return [
            'simple_html' => [Stream::string('my.html', 'HTML content')],
            'full_options' => [
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
                true,
                1.0,
                '1-2',
                Stream::string('my_header.html', 'Header content'),
                Stream::string('my_footer.html', 'Footer content'),
                '1s',
                "window.status === 'ready'",
                '#id',
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
                [499, 599],
                [499, 599],
                ['my.com'],
                true,
                true,
                true,
                SplitMode::intervals(1),
                'PDF/A-1a',
                true,
                ['Producer' => 'Gotenberg'],
                true,
                'my_user_password',
                'my_owner_password',
                [
                    Stream::string('my.xml', 'XML content'),
                    Stream::string('my_second.xml', 'Second XML content'),
                ],
                [
                    Stream::string('my.jpg', 'Image content'),
                ],
            ],
        ];
    }

    /**
     * @param ChromiumCookie[]                                  $cookies
     * @param array<string,string>                              $extraHttpHeaders
     * @param int[]                                             $failOnHttpStatusCodes
     * @param int[]                                             $failOnResourceHttpStatusCodes
     * @param string[]                                          $ignoreResourceHttpStatusDomains
     * @param Stream[]                                          $markdowns
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $embeds
     * @param Stream[]                                          $assets
     */
    #[Test]
    #[DataProvider('provideMarkdownData')]
    public function it_creates_a_valid_request_for_the_forms_chromium_convert_markdown_endpoint(
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
        bool $generateTaggedPdf = false,
        bool $printBackground = false,
        bool $omitBackground = false,
        bool $landscape = false,
        float|null $scale = null,
        string|null $nativePageRanges = null,
        Stream|null $header = null,
        Stream|null $footer = null,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $waitForSelector = null,
        string|null $emulatedMediaType = null,
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        array $ignoreResourceHttpStatusDomains = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        SplitMode|null $splitMode = null,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        bool $flatten = false,
        string $userPassword = '',
        string $ownerPassword = '',
        array $embeds = [],
        array $assets = [],
    ): void {
        $chromium = Gotenberg::chromium('')->pdf();
        $chromium = $this->hydrateChromiumPdfFormData(
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
            $generateTaggedPdf,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $waitForSelector,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $splitMode,
            $pdfa,
            $pdfua,
            $metadata,
            $flatten,
            $userPassword,
            $ownerPassword,
            $embeds,
            $assets,
        );

        $request = $chromium->markdown($index, ...$markdowns);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/chromium/convert/markdown', $request->getUri()->getPath());

        $index->getStream()->rewind();
        $this->assertContainsFormFile($body, 'index.html', $index->getStream()->getContents(), 'text/html');

        foreach ($markdowns as $markdown) {
            $markdown->getStream()->rewind();
            $this->assertContainsFormFile($body, $markdown->getFilename(), $markdown->getStream()->getContents());
        }

        $this->assertChromiumPdfOptions(
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
            $generateTaggedPdf,
            $printBackground,
            $omitBackground,
            $landscape,
            $scale,
            $nativePageRanges,
            $header,
            $footer,
            $waitDelay,
            $waitForExpression,
            $waitForSelector,
            $emulatedMediaType,
            $cookies,
            $userAgent,
            $extraHttpHeaders,
            $failOnHttpStatusCodes,
            $failOnResourceHttpStatusCodes,
            $ignoreResourceHttpStatusDomains,
            $failOnResourceLoadingFailed,
            $failOnConsoleExceptions,
            $skipNetworkIdleEvent,
            $splitMode,
            $pdfa,
            $pdfua,
            $metadata,
            $flatten,
            $userPassword,
            $ownerPassword,
            $embeds,
            $assets,
        );
    }

    /**
     * @return array<string, array{
     * Stream,
     * array<int, Stream>,
     * bool,
     * float|string|null,
     * float|string,
     * float|string|null,
     * float|string,
     * float|string,
     * float|string,
     * bool,
     * bool,
     * bool,
     * bool,
     * bool,
     * bool,
     * float|null,
     * string|null,
     * Stream|null,
     * Stream|null,
     * string|null,
     * string|null,
     * string|null,
     * string|null,
     * array<int, ChromiumCookie>,
     * string|null,
     * array<string, string>,
     * array<int, int>,
     * array<int, int>,
     * array<int, string>,
     * bool,
     * bool,
     * bool|null,
     * SplitMode|null,
     * string|null,
     * bool,
     * array<string, string|bool|float|int|array<string>>,
     * bool,
     * string,
     * string,
     * array<int, Stream>,
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
                true,
                1.0,
                '1-2',
                Stream::string('my_header.html', 'Header content'),
                Stream::string('my_footer.html', 'Footer content'),
                '1s',
                "window.status === 'ready'",
                '#id',
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
                [499, 599],
                [499, 599],
                ['my.com'],
                true,
                true,
                true,
                SplitMode::intervals(1),
                'PDF/A-1a',
                true,
                ['Producer' => 'Gotenberg'],
                true,
                'my_user_password',
                'my_owner_password',
                [
                    Stream::string('my.xml', 'XML content'),
                    Stream::string('my_second.xml', 'Second XML content'),
                ],
                [
                    Stream::string('my.jpg', 'Image content'),
                ],
            ],
        ];
    }

    /**
     * @param ChromiumCookie[]                                  $cookies
     * @param array<string,string>                              $extraHttpHeaders
     * @param int[]                                             $failOnHttpStatusCodes
     * @param int[]                                             $failOnResourceHttpStatusCodes
     * @param string[]                                          $ignoreResourceHttpStatusDomains
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $embeds
     * @param Stream[]                                          $assets
     */
    private function hydrateChromiumPdfFormData(
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
        bool $generateTaggedPdf = false,
        bool $printBackground = false,
        bool $omitBackground = false,
        bool $landscape = false,
        float|null $scale = null,
        string|null $nativePageRanges = null,
        Stream|null $header = null,
        Stream|null $footer = null,
        string|null $waitDelay = null,
        string|null $waitForExpression = null,
        string|null $waitForSelector = null,
        string|null $emulatedMediaType = null,
        array $cookies = [],
        string|null $userAgent = null,
        array $extraHttpHeaders = [],
        array $failOnHttpStatusCodes = [],
        array $failOnResourceHttpStatusCodes = [],
        array $ignoreResourceHttpStatusDomains = [],
        bool $failOnResourceLoadingFailed = false,
        bool $failOnConsoleExceptions = false,
        bool|null $skipNetworkIdleEvent = null,
        SplitMode|null $splitMode = null,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        bool $flatten = false,
        string $userPassword = '',
        string $ownerPassword = '',
        array $embeds = [],
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

        if ($generateTaggedPdf) {
            $chromium->generateTaggedPdf();
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

        if ($waitForSelector !== null) {
            $chromium->waitForSelector($waitForSelector);
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

        if ($splitMode !== null) {
            $chromium->split($splitMode);
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

        if ($flatten) {
            $chromium->flatten();
        }

        if ($userPassword !== '') {
            $chromium->encrypt($userPassword, $ownerPassword);
        }

        if (count($embeds) > 0) {
            $chromium->embeds(...$embeds);
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
     * @param string[]                                          $ignoreResourceHttpStatusDomains
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $embeds
     * @param Stream[]                                          $assets
     */
    private function assertChromiumPdfOptions(
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
        bool $generateTaggedPdf,
        bool $printBackground,
        bool $omitBackground,
        bool $landscape,
        float|null $scale,
        string|null $nativePageRanges,
        Stream|null $header,
        Stream|null $footer,
        string|null $waitDelay,
        string|null $waitForExpression,
        string|null $waitForSelector,
        string|null $emulatedMediaType,
        array $cookies,
        string|null $userAgent,
        array $extraHttpHeaders,
        array $failOnHttpStatusCodes,
        array $failOnResourceHttpStatusCodes,
        array $ignoreResourceHttpStatusDomains,
        bool $failOnResourceLoadingFailed,
        bool $failOnConsoleExceptions,
        bool|null $skipNetworkIdleEvent,
        SplitMode|null $splitMode,
        string|null $pdfa,
        bool $pdfua,
        array $metadata,
        bool $flatten,
        string $userPassword,
        string $ownerPassword,
        array $embeds,
        array $assets,
    ): void {
        if ($singlePage) {
            $this->assertContainsFormValue($body, 'singlePage', '1');
        }

        if ($paperWidth !== null) {
            $this->assertContainsFormValue($body, 'paperWidth', (string) $paperWidth);
            $this->assertContainsFormValue($body, 'paperHeight', (string) $paperHeight);
        }

        if ($marginTop !== null) {
            $this->assertContainsFormValue($body, 'marginTop', (string) $marginTop);
            $this->assertContainsFormValue($body, 'marginBottom', (string) $marginBottom);
            $this->assertContainsFormValue($body, 'marginLeft', (string) $marginLeft);
            $this->assertContainsFormValue($body, 'marginRight', (string) $marginRight);
        }

        if ($preferCssPageSize) {
            $this->assertContainsFormValue($body, 'preferCssPageSize', '1');
        }

        if ($generateDocumentOutline) {
            $this->assertContainsFormValue($body, 'generateDocumentOutline', '1');
        }

        if ($generateTaggedPdf) {
            $this->assertContainsFormValue($body, 'generateTaggedPdf', '1');
        }

        if ($printBackground) {
            $this->assertContainsFormValue($body, 'printBackground', '1');
        }

        if ($omitBackground) {
            $this->assertContainsFormValue($body, 'omitBackground', '1');
        }

        if ($landscape) {
            $this->assertContainsFormValue($body, 'landscape', '1');
        }

        if ($scale !== null) {
            $this->assertContainsFormValue($body, 'scale', (string) $scale);
        }

        if ($nativePageRanges !== null) {
            $this->assertContainsFormValue($body, 'nativePageRanges', $nativePageRanges);
        }

        if ($header !== null) {
            $header->getStream()->rewind();
            $this->assertContainsFormFile($body, 'header.html', $header->getStream()->getContents(), 'text/html');
        }

        if ($footer !== null) {
            $footer->getStream()->rewind();
            $this->assertContainsFormFile($body, 'footer.html', $footer->getStream()->getContents(), 'text/html');
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

        if ($splitMode !== null) {
            $this->assertContainsFormValue($body, 'splitMode', $splitMode->mode);
            $this->assertContainsFormValue($body, 'splitSpan', $splitMode->span);
            $this->assertContainsFormValue($body, 'splitUnify', $splitMode->unify ? '1' : '0');
        }

        if ($pdfa !== null) {
            $this->assertContainsFormValue($body, 'pdfa', $pdfa);
        }

        if ($pdfua) {
            $this->assertContainsFormValue($body, 'pdfua', '1');
        }

        if (count($metadata) > 0) {
            $json = json_encode($metadata);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'metadata', $json);
        }

        if ($flatten) {
            $this->assertContainsFormValue($body, 'flatten', '1');
        }

        if ($userPassword !== '') {
            $this->assertContainsFormValue($body, 'userPassword', $userPassword);
            $this->assertContainsFormValue($body, 'ownerPassword', $ownerPassword);
        }

        foreach ($embeds as $embed) {
            $embed->getStream()->rewind();
            $this->assertContainsFormFile($body, $embed->getFilename(), $embed->getStream()->getContents(), 'application/xml', 'embeds');
        }

        foreach ($assets as $asset) {
            $asset->getStream()->rewind();
            $this->assertContainsFormFile($body, $asset->getFilename(), $asset->getStream()->getContents(), 'image/jpeg');
        }
    }
}
