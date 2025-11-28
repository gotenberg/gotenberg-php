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

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, 'foo_' . $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }

        foreach ($embeds as $embed) {
            $embed->getStream()->rewind();
            $this->assertContainsFormFile($body, $embed->getFilename(), $embed->getStream()->getContents(), 'application/xml', 'embeds');
        }
    }

    /** @return array<string, array{array<int, Stream>, string|null, bool, array<string, array<string>|bool|float|int|string>, bool, string, string, array<int, Stream>}> */
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
            ],
        ];
    }

    /**
     * @param Stream[]                                          $pdfs
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $embeds
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

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            $this->assertContainsFormFile($body, $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }

        foreach ($embeds as $embed) {
            $embed->getStream()->rewind();
            $this->assertContainsFormFile($body, $embed->getFilename(), $embed->getStream()->getContents(), 'application/xml', 'embeds');
        }
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
     * array<int, Stream>
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
}
