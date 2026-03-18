<?php

declare(strict_types=1);

namespace Gotenberg\Test\Modules;

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Gotenberg;
use Gotenberg\SplitMode;
use Gotenberg\Stream;
use Gotenberg\Test\Helpers\Dummies\DummyIndex;
use Gotenberg\Test\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use function count;
use function json_encode;

final class PdfEnginesTest extends TestCase
{
    /**
     * @param Stream[]                                          $pdfs
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $embeds
     */
    #[Test]
    #[DataProvider('provideMergeData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_merge_endpoint(
        array $pdfs,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        bool $flatten = false,
        string $userPassword = '',
        string $ownerPassword = '',
        array $embeds = [],
        string $bookmarks = '',
        bool $autoIndexBookmarks = false,
        int $rotateAngle = 0,
        string $rotatePages = '',
    ): void {
        $pdfEngines = Gotenberg::pdfEngines('')->index(new DummyIndex());

        if ($pdfa !== null) {
            $pdfEngines->pdfa($pdfa);
        }

        if ($pdfua) {
            $pdfEngines->pdfua();
        }

        if (count($metadata) > 0) {
            $pdfEngines->metadata($metadata);
        }

        if ($flatten) {
            $pdfEngines->flattening();
        }

        if ($userPassword !== '') {
            $pdfEngines->encrypting($userPassword, $ownerPassword);
        }

        if (count($embeds) > 0) {
            $pdfEngines->embeds(...$embeds);
        }

        if ($bookmarks !== '') {
            $pdfEngines->bookmarks($bookmarks);
        }

        if ($autoIndexBookmarks) {
            $pdfEngines->autoIndexBookmarks();
        }

        if ($rotateAngle !== 0) {
            $pdfEngines->rotating($rotateAngle, $rotatePages);
        }

        $request = $pdfEngines->merge(...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/merge', $request->getUri()->getPath());

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

        if ($bookmarks !== '') {
            $this->assertContainsFormValue($body, 'bookmarks', $bookmarks);
        }

        if ($autoIndexBookmarks) {
            $this->assertContainsFormValue($body, 'autoIndexBookmarks', '1');
        }

        if ($rotateAngle !== 0) {
            $this->assertContainsFormValue($body, 'rotateAngle', (string) $rotateAngle);
        }

        if ($rotatePages !== '') {
            $this->assertContainsFormValue($body, 'rotatePages', $rotatePages);
        }

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, 'foo_' . $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }

        foreach ($embeds as $embed) {
            $embed->getStream()->rewind();
            $this->assertContainsFormFile($body, $embed->getFilename(), $embed->getStream()->getContents(), 'application/xml', 'embeds');
        }
    }

    /** @return array<string, array{array<int, Stream>, string|null, bool, array<string, array<string>|bool|float|int|string>, bool, string, string, array<int, Stream>, string, bool, int, string}> */
    public static function provideMergeData(): array
    {
        return [
            'basic' => [
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
            ],
            'full_options' => [
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                    Stream::string('my_third.pdf', 'Third PDF content'),
                ],
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
                '[{"title":"Chapter 1","page":1}]',
                true,
                90,
                '1-3,5',
            ],
        ];
    }

    /**
     * @param Stream[]                                          $pdfs
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $embeds
     * @param array<string,mixed>                               $watermarkOptions
     * @param array<string,mixed>                               $stampOptions
     */
    #[Test]
    #[DataProvider('provideSplitData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_split_endpoint(
        array $pdfs,
        SplitMode $mode,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        bool $flatten = false,
        string $userPassword = '',
        string $ownerPassword = '',
        array $embeds = [],
        string $watermarkSource = '',
        string $watermarkExpression = '',
        string $watermarkPages = '',
        array $watermarkOptions = [],
        Stream|null $watermarkFile = null,
        string $stampSource = '',
        string $stampExpression = '',
        string $stampPages = '',
        array $stampOptions = [],
        Stream|null $stampFile = null,
        int $rotateAngle = 0,
        string $rotatePages = '',
    ): void {
        $pdfEngines = Gotenberg::pdfEngines('');

        if ($pdfa !== null) {
            $pdfEngines->pdfa($pdfa);
        }

        if ($pdfua) {
            $pdfEngines->pdfua();
        }

        if (count($metadata) > 0) {
            $pdfEngines->metadata($metadata);
        }

        if ($flatten) {
            $pdfEngines->flattening();
        }

        if ($userPassword !== '') {
            $pdfEngines->encrypting($userPassword, $ownerPassword);
        }

        if (count($embeds) > 0) {
            $pdfEngines->embeds(...$embeds);
        }

        if ($watermarkSource !== '') {
            $pdfEngines->watermarking($watermarkSource, $watermarkExpression, $watermarkPages, $watermarkOptions);
        }

        if ($watermarkFile !== null) {
            $pdfEngines->watermarkFile($watermarkFile);
        }

        if ($stampSource !== '') {
            $pdfEngines->stamping($stampSource, $stampExpression, $stampPages, $stampOptions);
        }

        if ($stampFile !== null) {
            $pdfEngines->stampFile($stampFile);
        }

        if ($rotateAngle !== 0) {
            $pdfEngines->rotating($rotateAngle, $rotatePages);
        }

        $request = $pdfEngines->split($mode, ...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/split', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'splitMode', $mode->mode);
        $this->assertContainsFormValue($body, 'splitSpan', $mode->span);
        $this->assertContainsFormValue($body, 'splitUnify', $mode->unify ? '1' : '0');

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

        if ($watermarkSource !== '') {
            $this->assertContainsFormValue($body, 'watermarkSource', $watermarkSource);
        }

        if ($watermarkExpression !== '') {
            $this->assertContainsFormValue($body, 'watermarkExpression', $watermarkExpression);
        }

        if ($watermarkPages !== '') {
            $this->assertContainsFormValue($body, 'watermarkPages', $watermarkPages);
        }

        if (count($watermarkOptions) > 0) {
            $json = json_encode($watermarkOptions);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'watermarkOptions', $json);
        }

        if ($stampSource !== '') {
            $this->assertContainsFormValue($body, 'stampSource', $stampSource);
        }

        if ($stampExpression !== '') {
            $this->assertContainsFormValue($body, 'stampExpression', $stampExpression);
        }

        if ($stampPages !== '') {
            $this->assertContainsFormValue($body, 'stampPages', $stampPages);
        }

        if (count($stampOptions) > 0) {
            $json = json_encode($stampOptions);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'stampOptions', $json);
        }

        if ($rotateAngle !== 0) {
            $this->assertContainsFormValue($body, 'rotateAngle', (string) $rotateAngle);
        }

        if ($rotatePages !== '') {
            $this->assertContainsFormValue($body, 'rotatePages', $rotatePages);
        }

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }

        foreach ($embeds as $embed) {
            $embed->getStream()->rewind();
            $this->assertContainsFormFile($body, $embed->getFilename(), $embed->getStream()->getContents(), 'application/xml', 'embeds');
        }

        if ($watermarkFile !== null) {
            $watermarkFile->getStream()->rewind();
            $this->assertContainsFormFile($body, $watermarkFile->getFilename(), $watermarkFile->getStream()->getContents(), 'application/pdf', 'watermark');
        }

        if ($stampFile === null) {
            return;
        }

        $stampFile->getStream()->rewind();
        $this->assertContainsFormFile($body, $stampFile->getFilename(), $stampFile->getStream()->getContents(), 'application/pdf', 'stamp');
    }

    /**
     * @return array<string, array{
     * array<int, Stream>,
     * SplitMode,
     * string|null,
     * bool,
     * array<string, string|bool|float|int|array<string>>,
     * bool,
     * string,
     * string,
     * array<int, Stream>,
     * string,
     * string,
     * string,
     * array<string, string>,
     * Stream|null,
     * string,
     * string,
     * string,
     * array<string, string>,
     * Stream|null,
     * int,
     * string
     * }>
     */
    public static function provideSplitData(): array
    {
        return [
            'intervals' => [
                [
                    Stream::string('my.pdf', 'PDF content'),
                ],
                SplitMode::intervals(1),
            ],
            'pages_full_options' => [
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                    Stream::string('my_third.pdf', 'Third PDF content'),
                ],
                SplitMode::pages('1-2', true),
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
                'my_watermark_source',
                'my_watermark_expression',
                '1-2',
                ['key' => 'value'],
                Stream::string('my_watermark.pdf', 'Watermark content'),
                'my_stamp_source',
                'my_stamp_expression',
                '3-4',
                ['key' => 'value'],
                Stream::string('my_stamp.pdf', 'Stamp content'),
                180,
                '1-3',
            ],
        ];
    }

    #[Test]
    #[DataProvider('provideConvertData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_convert_endpoint(string $pdfa, bool $pdfua, Stream ...$pdfs): void
    {
        $pdfEngines = Gotenberg::pdfEngines('');

        if ($pdfua) {
            $pdfEngines->pdfua();
        }

        $request = $pdfEngines->convert($pdfa, ...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/convert', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'pdfa', $pdfa);

        if ($pdfua) {
            $this->assertContainsFormValue($body, 'pdfua', '1');
        }

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    }

    /** @return array<string, array{string, bool, Stream[]}> */
    public static function provideConvertData(): array
    {
        return [
            'basic' => [
                'PDF/A-1a',
                false,
                Stream::string('my.pdf', 'PDF content'),
            ],
            'with_pdfua' => [
                'PDF/A-1a',
                true,
                Stream::string('my.pdf', 'PDF content'),
                Stream::string('my_second.pdf', 'Second PDF content'),
            ],
        ];
    }

    /** @param Stream[] $pdfs */
    #[Test]
    #[DataProvider('provideFlattenData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_flatten_endpoint(array $pdfs): void
    {
        $pdfEngines = Gotenberg::pdfEngines('');

        $request = $pdfEngines->flatten(...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/flatten', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'flatten', '1');

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    }

    /** @return array<string, array{array<int, Stream>}> */
    public static function provideFlattenData(): array
    {
        return [
            'basic' => [
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
            ],
        ];
    }

    /** @param Stream[] $pdfs */
    #[Test]
    #[DataProvider('provideReadMetadataData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_metadata_read_endpoint(array $pdfs): void
    {
        $pdfEngines = Gotenberg::pdfEngines('');

        $request = $pdfEngines->readMetadata(...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/metadata/read', $request->getUri()->getPath());

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    }

    /** @return array<string, array{array<int, Stream>}> */
    public static function provideReadMetadataData(): array
    {
        return [
            'basic' => [
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
            ],
        ];
    }

    /**
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $pdfs
     */
    #[Test]
    #[DataProvider('provideWriteMetadataData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_metadata_write_endpoint(array $metadata, array $pdfs): void
    {
        $pdfEngines = Gotenberg::pdfEngines('');

        $request = $pdfEngines->writeMetadata($metadata, ...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/metadata/write', $request->getUri()->getPath());

        $json = json_encode($metadata);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        $this->assertContainsFormValue($body, 'metadata', $json);

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    }

    /** @return array<string, array{array<string, string|bool|float|int|array<string>>, array<int, Stream>}> */
    public static function provideWriteMetadataData(): array
    {
        return [
            'basic' => [
                ['Producer' => 'Gotenberg'],
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
            ],
        ];
    }

    /** @param Stream[] $pdfs */
    #[Test]
    #[DataProvider('provideEncryptData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_encrypt_endpoint(string $userPassword, string $ownerPassword, array $pdfs): void
    {
        $pdfEngines = Gotenberg::pdfEngines('');

        $request = $pdfEngines->encrypt($userPassword, $ownerPassword, ...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/encrypt', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'userPassword', $userPassword);
        $this->assertContainsFormValue($body, 'ownerPassword', $ownerPassword);

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    }

    /** @return array<string, array{string, string, array<int, Stream>}> */
    public static function provideEncryptData(): array
    {
        return [
            'basic' => [
                'my_user_password',
                'my_owner_password',
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
            ],
        ];
    }

    /**
     * @param Stream[] $pdfs
     * @param Stream[] $embeds
     */
    #[Test]
    #[DataProvider('provideEmbedData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_embed_endpoint(array $embeds, array $pdfs): void
    {
        $pdfEngines = Gotenberg::pdfEngines('');

        $request = $pdfEngines->embed($embeds, ...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/embed', $request->getUri()->getPath());

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }

        foreach ($embeds as $embed) {
            $embed->getStream()->rewind();
            $this->assertContainsFormFile($body, $embed->getFilename(), $embed->getStream()->getContents(), 'application/xml', 'embeds');
        }
    }

    /** @return array<string, array{array<int, Stream>, array<int, Stream>}> */
    public static function provideEmbedData(): array
    {
        return [
            'basic' => [
                [
                    Stream::string('my.xml', 'XML content'),
                    Stream::string('my_second.xml', 'Second XML content'),
                ],
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
            ],
        ];
    }

    /**
     * @param Stream[]            $pdfs
     * @param array<string,mixed> $options
     */
    #[Test]
    #[DataProvider('provideWatermarkData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_watermark_endpoint(
        string $source,
        array $pdfs,
        string $expression = '',
        string $pages = '',
        array $options = [],
        Stream|null $watermarkFile = null,
    ): void {
        $pdfEngines = Gotenberg::pdfEngines('');

        if ($expression !== '') {
            $pdfEngines->watermarking($source, $expression, $pages, $options);
        }

        if ($watermarkFile !== null) {
            $pdfEngines->watermarkFile($watermarkFile);
        }

        $request = $pdfEngines->watermark($source, ...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/watermark', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'watermarkSource', $source);

        if ($expression !== '') {
            $this->assertContainsFormValue($body, 'watermarkExpression', $expression);
        }

        if ($pages !== '') {
            $this->assertContainsFormValue($body, 'watermarkPages', $pages);
        }

        if (count($options) > 0) {
            $json = json_encode($options);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'watermarkOptions', $json);
        }

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }

        if ($watermarkFile === null) {
            return;
        }

        $watermarkFile->getStream()->rewind();
        $this->assertContainsFormFile($body, $watermarkFile->getFilename(), $watermarkFile->getStream()->getContents(), 'application/pdf', 'watermark');
    }

    /** @return array<string, array{0: string, 1: array<int, Stream>, 2?: string, 3?: string, 4?: array<string, string>, 5?: Stream}> */
    public static function provideWatermarkData(): array
    {
        return [
            'basic' => [
                'my_source',
                [
                    Stream::string('my.pdf', 'PDF content'),
                ],
            ],
            'full_options' => [
                'my_source',
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
                'my_expression',
                '1-2',
                ['key' => 'value'],
                Stream::string('my_watermark.pdf', 'Watermark content'),
            ],
        ];
    }

    /**
     * @param Stream[]            $pdfs
     * @param array<string,mixed> $options
     */
    #[Test]
    #[DataProvider('provideStampData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_stamp_endpoint(
        string $source,
        array $pdfs,
        string $expression = '',
        string $pages = '',
        array $options = [],
        Stream|null $stampFile = null,
    ): void {
        $pdfEngines = Gotenberg::pdfEngines('');

        if ($expression !== '') {
            $pdfEngines->stamping($source, $expression, $pages, $options);
        }

        if ($stampFile !== null) {
            $pdfEngines->stampFile($stampFile);
        }

        $request = $pdfEngines->stamp($source, ...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/stamp', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'stampSource', $source);

        if ($expression !== '') {
            $this->assertContainsFormValue($body, 'stampExpression', $expression);
        }

        if ($pages !== '') {
            $this->assertContainsFormValue($body, 'stampPages', $pages);
        }

        if (count($options) > 0) {
            $json = json_encode($options);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'stampOptions', $json);
        }

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }

        if ($stampFile === null) {
            return;
        }

        $stampFile->getStream()->rewind();
        $this->assertContainsFormFile($body, $stampFile->getFilename(), $stampFile->getStream()->getContents(), 'application/pdf', 'stamp');
    }

    /** @return array<string, array{0: string, 1: array<int, Stream>, 2?: string, 3?: string, 4?: array<string, string>, 5?: Stream}> */
    public static function provideStampData(): array
    {
        return [
            'basic' => [
                'my_source',
                [
                    Stream::string('my.pdf', 'PDF content'),
                ],
            ],
            'full_options' => [
                'my_source',
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
                'my_expression',
                '1-2',
                ['key' => 'value'],
                Stream::string('my_stamp.pdf', 'Stamp content'),
            ],
        ];
    }

    /** @param Stream[] $pdfs */
    #[Test]
    #[DataProvider('provideRotateData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_rotate_endpoint(
        int $angle,
        array $pdfs,
        string $pages = '',
    ): void {
        $pdfEngines = Gotenberg::pdfEngines('');

        if ($pages !== '') {
            $pdfEngines->rotating($angle, $pages);
        }

        $request = $pdfEngines->rotate($angle, ...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/rotate', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'rotateAngle', (string) $angle);

        if ($pages !== '') {
            $this->assertContainsFormValue($body, 'rotatePages', $pages);
        }

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    }

    /** @return array<string, array{0: int, 1: array<int, Stream>, 2?: string}> */
    public static function provideRotateData(): array
    {
        return [
            'basic' => [
                90,
                [
                    Stream::string('my.pdf', 'PDF content'),
                ],
            ],
            'full_options' => [
                270,
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
                '1-3,5',
            ],
        ];
    }

    /** @param Stream[] $pdfs */
    #[Test]
    #[DataProvider('provideReadBookmarksData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_bookmarks_read_endpoint(array $pdfs): void
    {
        $pdfEngines = Gotenberg::pdfEngines('');

        $request = $pdfEngines->readBookmarks(...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/bookmarks/read', $request->getUri()->getPath());

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    }

    /** @return array<string, array{array<int, Stream>}> */
    public static function provideReadBookmarksData(): array
    {
        return [
            'basic' => [
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
            ],
        ];
    }

    /** @param Stream[] $pdfs */
    #[Test]
    #[DataProvider('provideWriteBookmarksData')]
    public function it_creates_a_valid_request_for_the_forms_pdfengines_bookmarks_write_endpoint(string $bookmarks, array $pdfs): void
    {
        $pdfEngines = Gotenberg::pdfEngines('');

        $request = $pdfEngines->writeBookmarks($bookmarks, ...$pdfs);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/pdfengines/bookmarks/write', $request->getUri()->getPath());
        $this->assertContainsFormValue($body, 'bookmarks', $bookmarks);

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    }

    /** @return array<string, array{string, array<int, Stream>}> */
    public static function provideWriteBookmarksData(): array
    {
        return [
            'basic' => [
                '[{"title":"Chapter 1","page":1}]',
                [
                    Stream::string('my.pdf', 'PDF content'),
                    Stream::string('my_second.pdf', 'Second PDF content'),
                ],
            ],
        ];
    }
}
