<?php

declare(strict_types=1);

namespace Gotenberg\Test\Modules;

use Closure;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Gotenberg\Test\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;

final class ChromiumScreenshotTest extends TestCase
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
        yield 'url' => [
            (static fn (): RequestInterface => Gotenberg::chromium('https://my.url')
                ->screenshot()
                ->width(1280)
                ->height(720)
                ->clip()
                ->deviceScaleFactor(2.0)
                ->jpeg()
                ->quality(80)
                ->optimizeForSpeed()
                ->selector('#main')
                ->waitDelay('1s')
                ->url('https://my.url')),
            '/forms/chromium/screenshot/url',
            [
                ['url', 'https://my.url'],
                ['width', '1280'],
                ['height', '720'],
                ['clip', 'true'],
                ['deviceScaleFactor', '2'],
                ['format', 'jpeg'],
                ['quality', '80'],
                ['optimizeForSpeed', 'true'],
                ['selector', '#main'],
                ['waitDelay', '1s'],
            ],
        ];

        yield 'html' => [
            (static fn (): RequestInterface => Gotenberg::chromium('https://my.url')
                ->screenshot()
                ->png()
                ->html(Stream::string('my.html', 'HTML content'))),
            '/forms/chromium/screenshot/html',
            [['format', 'png']],
            [['files', 'index.html', 'HTML content', 'text/html']],
        ];

        yield 'markdown' => [
            (static fn (): RequestInterface => Gotenberg::chromium('https://my.url')
                ->screenshot()
                ->webp()
                ->markdown(Stream::string('my.html', 'HTML content'), Stream::string('my.md', 'Markdown content'))),
            '/forms/chromium/screenshot/markdown',
            [['format', 'webp']],
            [
                ['files', 'index.html', 'HTML content', 'text/html'],
                ['files', 'my.md', 'Markdown content'],
            ],
        ];
    }
}
