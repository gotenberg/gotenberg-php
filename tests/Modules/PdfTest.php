<?php

declare(strict_types=1);

namespace Gotenberg\Test\Modules;

use Closure;
use Gotenberg\Gotenberg;
use Gotenberg\Permission;
use Gotenberg\Profile;
use Gotenberg\Stage;
use Gotenberg\Stream;
use Gotenberg\Test\Helpers\Dummies\DummyIndex;
use Gotenberg\Test\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;

final class PdfTest extends TestCase
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
        yield 'pipe' => [
            (static fn (): RequestInterface => Gotenberg::pdf('https://my.url')
                ->index(new DummyIndex())
                ->stages(
                    Stage::merge(),
                    Stage::stampText('DRAFT'),
                    Stage::stampPdf(Stream::string('letterhead.pdf', 'Letterhead content')),
                )
                ->pipe(Stream::string('report.pdf', 'Report content'), Stream::string('annex.pdf', 'Annex content'))),
            '/forms/pdf/pipe',
            [['stages', '[{"op":"merge"},{"op":"stamp-text","text":"DRAFT"},{"op":"stamp-pdf","source":"letterhead.pdf"}]']],
            [
                ['files', 'letterhead.pdf', 'Letterhead content', 'application/pdf'],
                ['files', '0_report.pdf', 'Report content', 'application/pdf'],
                ['files', '1_annex.pdf', 'Annex content', 'application/pdf'],
            ],
        ];

        yield 'pipe without merge keeps the names' => [
            (static fn (): RequestInterface => Gotenberg::pdf('https://my.url')
                ->stages(Stage::splitEvery(1))
                ->pipe(Stream::string('report.pdf', 'Report content'))),
            '/forms/pdf/pipe',
            [['stages', '[{"op":"split","every":1}]']],
            [['files', 'report.pdf', 'Report content', 'application/pdf']],
        ];

        yield 'run merge' => [
            (static fn (): RequestInterface => Gotenberg::pdf('https://my.url')
                ->index(new DummyIndex())
                ->run(Stage::merge(), Stream::string('b.pdf', 'B content'), Stream::string('a.pdf', 'A content'))),
            '/forms/pdf/merge',
            [],
            [
                ['files', '0_b.pdf', 'B content', 'application/pdf'],
                ['files', '1_a.pdf', 'A content', 'application/pdf'],
            ],
        ];

        yield 'run stamp image' => [
            (static fn (): RequestInterface => Gotenberg::pdf('https://my.url')
                ->run(
                    Stage::stampImage(Stream::string('logo.png', 'Logo content'), pages: '1', opacity: 0.5),
                    Stream::string('report.pdf', 'Report content'),
                )),
            '/forms/pdf/stamp-image',
            [
                ['image', 'logo.png'],
                ['pages', '1'],
                ['opacity', '0.5'],
            ],
            [
                ['files', 'logo.png', 'Logo content', 'image/png'],
                ['files', 'report.pdf', 'Report content', 'application/pdf'],
            ],
        ];

        yield 'run encrypt' => [
            (static fn (): RequestInterface => Gotenberg::pdf('https://my.url')
                ->run(Stage::encrypt('user', deny: [Permission::Print]), Stream::string('report.pdf', 'Report content'))),
            '/forms/pdf/encrypt',
            [
                ['userPassword', 'user'],
                ['permissions', '{"print":false}'],
            ],
            [['files', 'report.pdf', 'Report content', 'application/pdf']],
        ];

        foreach (['info', 'metadata', 'bookmarks', 'attachments'] as $route) {
            yield $route => [
                (static fn (): RequestInterface => Gotenberg::pdf('https://my.url')
                    ->password('secret')
                    ->{$route}(Stream::string('report.pdf', 'Report content'))),
                '/forms/pdf/' . $route,
                [['password', 'secret']],
                [['files', 'report.pdf', 'Report content', 'application/pdf']],
            ];
        }

        yield 'form' => [
            (static fn (): RequestInterface => Gotenberg::pdf('https://my.url')->form('1-2', Stream::string('fields.pdf', 'Form content'))),
            '/forms/pdf/form',
            [['pages', '1-2']],
            [['files', 'fields.pdf', 'Form content', 'application/pdf']],
        ];

        yield 'validate a profile' => [
            (static fn (): RequestInterface => Gotenberg::pdf('https://my.url')->validate(Profile::PdfA3b, Stream::string('invoice.pdf', 'Invoice content'))),
            '/forms/pdf/validate',
            [['profile', 'PDF/A-3b']],
            [['files', 'invoice.pdf', 'Invoice content', 'application/pdf']],
        ];

        yield 'validate a pair of profiles' => [
            (static fn (): RequestInterface => Gotenberg::pdf('https://my.url')->validate([Profile::PdfA2b, Profile::PdfUa1], Stream::string('archive.pdf', 'Archive content'))),
            '/forms/pdf/validate',
            [['profile', '["PDF\/A-2b","PDF\/UA-1"]']],
            [['files', 'archive.pdf', 'Archive content', 'application/pdf']],
        ];
    }
}
