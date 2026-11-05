<?php

declare(strict_types=1);

namespace Gotenberg\Test\Modules;

use Closure;
use Gotenberg\ConsoleMessageType;
use Gotenberg\Gotenberg;
use Gotenberg\Modules\ChromiumCookie;
use Gotenberg\PaperSize;
use Gotenberg\SameSite;
use Gotenberg\Stage;
use Gotenberg\Stream;
use Gotenberg\Test\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;

final class ChromiumPdfTest extends TestCase
{
    /**
     * @param Closure(): RequestInterface                                   $build
     * @param list<array{0: string, 1: string}>                             $values
     * @param list<array{0: string, 1: string, 2: string, 3?: string|null}> $files
     */
    #[Test]
    #[DataProvider('provideRequestData')]
    public function it_creates_a_valid_request(Closure $build, string $endpoint, array $values, array $files = []): void
    {
        $this->assertRequest($build(), $endpoint, $values, $files);
    }

    /** @return iterable<string, array{0: Closure(): RequestInterface, 1: string, 2: list<array{0: string, 1: string}>, 3?: list<array{0: string, 1: string, 2: string, 3?: string|null}>}> */
    public static function provideRequestData(): iterable
    {
        yield 'url with every option' => [
            (static fn (): RequestInterface => Gotenberg::chromium('https://my.url/')
                ->pdf()
                ->singlePage()
                ->paperSize(PaperSize::A4)
                ->paperDimensions(8.27, '11.7in')
                ->margins(1, 2, '3mm', 4)
                ->preferCssPageSize()
                ->nativeBookmarks()
                ->nativeTaggedPdf()
                ->printBackground()
                ->landscape()
                ->scale(1.5)
                ->nativePages('1-5, 8')
                ->header(Stream::string('my_header.html', 'Header content'))
                ->footer(Stream::string('my_footer.html', 'Footer content'))
                ->omitBackground()
                ->waitDelay('3s')
                ->waitForExpression("window.status === 'ready'")
                ->waitForSelector('#id')
                ->waitForWindowStatus('ready')
                ->emulatePrintMediaType()
                ->emulatedMediaFeatures(['prefers-color-scheme' => 'dark'])
                ->cookies(new ChromiumCookie('yummy_cookie', 'choco', 'theyummycookie.com', path: '/', secure: true, httpOnly: true, sameSite: SameSite::Lax))
                ->userAgent('Gotenberg')
                ->extraHttpHeaders(['My-Http-Header' => 'HTTP header content'])
                ->failOnHttpStatusCodes(499, 599)
                ->failOnResourceHttpStatusCodes(404)
                ->ignoreResourceHttpStatusDomains('example.com')
                ->failOnResourceLoadingFailed()
                ->failOnConsoleExceptions()
                ->failOnConsoleMessageTypes(ConsoleMessageType::Error, ConsoleMessageType::Warning)
                ->skipNetworkIdleEvent(false)
                ->skipNetworkAlmostIdleEvent()
                ->assets(Stream::string('my.jpg', 'Image content'))
                ->url('https://my.url')),
            '/forms/chromium/convert/url',
            [
                ['url', 'https://my.url'],
                ['singlePage', 'true'],
                ['paperSize', 'A4'],
                ['paperWidth', '8.27'],
                ['paperHeight', '11.7in'],
                ['marginTop', '1'],
                ['marginBottom', '2'],
                ['marginLeft', '3mm'],
                ['marginRight', '4'],
                ['preferCssPageSize', 'true'],
                ['nativeBookmarks', 'true'],
                ['nativeTaggedPdf', 'true'],
                ['printBackground', 'true'],
                ['landscape', 'true'],
                ['scale', '1.5'],
                ['nativePages', '1-5, 8'],
                ['omitBackground', 'true'],
                ['waitDelay', '3s'],
                ['waitForExpression', "window.status === 'ready'"],
                ['waitForSelector', '#id'],
                ['waitForWindowStatus', 'ready'],
                ['emulatedMediaType', 'print'],
                ['emulatedMediaFeatures', '{"prefers-color-scheme":"dark"}'],
                ['cookies', '[{"name":"yummy_cookie","value":"choco","domain":"theyummycookie.com","path":"\/","secure":true,"httpOnly":true,"sameSite":"Lax"}]'],
                ['userAgent', 'Gotenberg'],
                ['extraHttpHeaders', '{"My-Http-Header":"HTTP header content"}'],
                ['failOnHttpStatusCodes', '[499,599]'],
                ['failOnResourceHttpStatusCodes', '[404]'],
                ['ignoreResourceHttpStatusDomains', '["example.com"]'],
                ['failOnResourceLoadingFailed', 'true'],
                ['failOnConsoleExceptions', 'true'],
                ['failOnConsoleMessageTypes', '["error","warning"]'],
                ['skipNetworkIdleEvent', 'false'],
                ['skipNetworkAlmostIdleEvent', 'true'],
            ],
            [
                ['files', 'header.html', 'Header content', 'text/html'],
                ['files', 'footer.html', 'Footer content', 'text/html'],
                ['files', 'my.jpg', 'Image content', 'image/jpeg'],
            ],
        ];

        yield 'html with stages' => [
            (static fn (): RequestInterface => Gotenberg::chromium('https://my.url')
                ->pdf()
                ->emulateScreenMediaType()
                ->stages(
                    Stage::stampImage(Stream::string('logo.png', 'Logo content'), x: 72, y: 72),
                    Stage::optimize(),
                )
                ->html(Stream::string('my.html', 'HTML content'))),
            '/forms/chromium/convert/html',
            [
                ['emulatedMediaType', 'screen'],
                ['stages', '[{"op":"stamp-image","image":"logo.png","x":72,"y":72},{"op":"optimize"}]'],
            ],
            [
                ['files', 'index.html', 'HTML content', 'text/html'],
                ['files', 'logo.png', 'Logo content', 'image/png'],
            ],
        ];

        yield 'html from an archive' => [
            (static fn (): RequestInterface => Gotenberg::chromium('https://my.url')
                ->pdf()
                ->archives(Stream::string('site.zip', 'ZIP content'))
                ->html(null)),
            '/forms/chromium/convert/html',
            [],
            [['archives', 'site.zip', 'ZIP content']],
        ];

        yield 'markdown' => [
            (static fn (): RequestInterface => Gotenberg::chromium('https://my.url')
                ->pdf()
                ->markdown(
                    Stream::string('my.html', 'HTML content'),
                    Stream::string('my.md', 'Markdown content'),
                    Stream::string('my_second.md', 'Second markdown content'),
                )),
            '/forms/chromium/convert/markdown',
            [],
            [
                ['files', 'index.html', 'HTML content', 'text/html'],
                ['files', 'my.md', 'Markdown content'],
                ['files', 'my_second.md', 'Second markdown content'],
            ],
        ];
    }
}
