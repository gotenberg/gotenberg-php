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

final class LibreOfficeTest extends TestCase
{
    /**
     * @param Stream[]                                          $files
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[]                                          $embeds
     * @param array<string,mixed>                               $watermarkOptions
     * @param array<string,mixed>                               $stampOptions
     */
    #[Test]
    #[DataProvider('provideConvertData')]
    public function it_creates_a_valid_request_for_the_forms_libreoffice_convert_endpoint(
        array $files,
        string|null $password = null,
        bool $landscape = false,
        string|null $nativePageRanges = null,
        bool|null $updateIndexes = null,
        bool|null $exportFormFields = null,
        bool $allowDuplicateFieldNames = false,
        bool|null $exportBookmarks = null,
        bool $exportBookmarksToPdfDestination = false,
        bool $exportPlaceholders = false,
        bool $exportNotes = false,
        bool $exportNotesPages = false,
        bool $exportOnlyNotesPages = false,
        bool $exportNotesInMargin = false,
        bool $convertOooTargetToPdfTarget = false,
        bool $exportLinksRelativeFsys = false,
        bool $exportHiddenSlides = false,
        bool $skipEmptyPages = false,
        bool $addOriginalDocumentAsStream = false,
        bool $singlePageSheets = false,
        bool $losslessImageCompression = false,
        int|null $quality = null,
        bool $reduceImageResolution = false,
        int|null $maxImageResolution = null,
        SplitMode|null $splitMode = null,
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        bool $merge = false,
        string|null $nativeWatermarkText = null,
        int|null $nativeWatermarkColor = null,
        int|null $nativeWatermarkFontHeight = null,
        int|null $nativeWatermarkRotateAngle = null,
        string|null $nativeWatermarkFontName = null,
        string|null $nativeTiledWatermarkText = null,
        bool $flatten = false,
        string $userPassword = '',
        string $ownerPassword = '',
        array $embeds = [],
        string $watermarkSource = '',
        string $watermarkExpression = '',
        string $watermarkPages = '',
        array $watermarkOptions = [],
        string $stampSource = '',
        string $stampExpression = '',
        string $stampPages = '',
        array $stampOptions = [],
        int $rotateAngle = 0,
        string $rotatePages = '',
    ): void {
        $libreOffice = Gotenberg::libreOffice('');

        if ($password !== null) {
            $libreOffice->password($password);
        }

        if ($landscape) {
            $libreOffice->landscape();
        }

        if ($nativePageRanges !== null) {
            $libreOffice->nativePageRanges($nativePageRanges);
        }

        if ($updateIndexes !== null) {
            $libreOffice->updateIndexes($updateIndexes);
        }

        if ($exportFormFields !== null) {
            $libreOffice->exportFormFields($exportFormFields);
        }

        if ($allowDuplicateFieldNames) {
            $libreOffice->allowDuplicateFieldNames();
        }

        if ($exportBookmarks !== null) {
            $libreOffice->exportBookmarks($exportBookmarks);
        }

        if ($exportBookmarksToPdfDestination) {
            $libreOffice->exportBookmarksToPdfDestination();
        }

        if ($exportPlaceholders) {
            $libreOffice->exportPlaceholders();
        }

        if ($exportNotes) {
            $libreOffice->exportNotes();
        }

        if ($exportNotesPages) {
            $libreOffice->exportNotesPages();
        }

        if ($exportOnlyNotesPages) {
            $libreOffice->exportOnlyNotesPages();
        }

        if ($exportNotesInMargin) {
            $libreOffice->exportNotesInMargin();
        }

        if ($convertOooTargetToPdfTarget) {
            $libreOffice->convertOooTargetToPdfTarget();
        }

        if ($exportLinksRelativeFsys) {
            $libreOffice->exportLinksRelativeFsys();
        }

        if ($exportHiddenSlides) {
            $libreOffice->exportHiddenSlides();
        }

        if ($skipEmptyPages) {
            $libreOffice->skipEmptyPages();
        }

        if ($addOriginalDocumentAsStream) {
            $libreOffice->addOriginalDocumentAsStream();
        }

        if ($singlePageSheets) {
            $libreOffice->singlePageSheets();
        }

        if ($losslessImageCompression) {
            $libreOffice->losslessImageCompression();
        }

        if ($quality !== null) {
            $libreOffice->quality($quality);
        }

        if ($reduceImageResolution) {
            $libreOffice->reduceImageResolution();
        }

        if ($maxImageResolution !== null) {
            $libreOffice->maxImageResolution($maxImageResolution);
        }

        if ($nativeWatermarkText !== null) {
            $libreOffice->nativeWatermarkText($nativeWatermarkText);
        }

        if ($nativeWatermarkColor !== null) {
            $libreOffice->nativeWatermarkColor($nativeWatermarkColor);
        }

        if ($nativeWatermarkFontHeight !== null) {
            $libreOffice->nativeWatermarkFontHeight($nativeWatermarkFontHeight);
        }

        if ($nativeWatermarkRotateAngle !== null) {
            $libreOffice->nativeWatermarkRotateAngle($nativeWatermarkRotateAngle);
        }

        if ($nativeWatermarkFontName !== null) {
            $libreOffice->nativeWatermarkFontName($nativeWatermarkFontName);
        }

        if ($nativeTiledWatermarkText !== null) {
            $libreOffice->nativeTiledWatermarkText($nativeTiledWatermarkText);
        }

        if ($pdfa !== null) {
            $libreOffice->pdfa($pdfa);
        }

        if ($splitMode !== null) {
            $libreOffice->split($splitMode);
        }

        if ($pdfua) {
            $libreOffice->pdfua();
        }

        if (count($metadata) > 0) {
            $libreOffice->metadata($metadata);
        }

        if ($merge) {
            $libreOffice
                ->index(new DummyIndex())
                ->merge();
        }

        if ($flatten) {
            $libreOffice->flatten();
        }

        if ($userPassword !== '') {
            $libreOffice->encrypt($userPassword, $ownerPassword);
        }

        if (count($embeds) > 0) {
            $libreOffice->embeds(...$embeds);
        }

        if ($watermarkSource !== '') {
            $libreOffice->watermarking($watermarkSource, $watermarkExpression, $watermarkPages, $watermarkOptions);
        }

        if ($stampSource !== '') {
            $libreOffice->stamping($stampSource, $stampExpression, $stampPages, $stampOptions);
        }

        if ($rotateAngle !== 0) {
            $libreOffice->rotating($rotateAngle, $rotatePages);
        }

        $request = $libreOffice->convert(...$files);
        $body    = $this->sanitize($request->getBody()->getContents());

        $this->assertSame('/forms/libreoffice/convert', $request->getUri()->getPath());

        if ($password !== null) {
            $this->assertContainsFormValue($body, 'password', $password);
        }

        if ($landscape) {
            $this->assertContainsFormValue($body, 'landscape', '1');
        }

        if ($nativePageRanges !== null) {
            $this->assertContainsFormValue($body, 'nativePageRanges', $nativePageRanges);
        }

        if ($updateIndexes !== null) {
            $this->assertContainsFormValue($body, 'updateIndexes', $updateIndexes ? '1' : '0');
        }

        if ($exportFormFields !== null) {
            $this->assertContainsFormValue($body, 'exportFormFields', $exportFormFields ? '1' : '0');
        }

        if ($allowDuplicateFieldNames) {
            $this->assertContainsFormValue($body, 'allowDuplicateFieldNames', '1');
        }

        if ($exportBookmarks !== null) {
            $this->assertContainsFormValue($body, 'exportBookmarks', $exportBookmarks ? '1' : '0');
        }

        if ($exportBookmarksToPdfDestination) {
            $this->assertContainsFormValue($body, 'exportBookmarksToPdfDestination', '1');
        }

        if ($exportPlaceholders) {
            $this->assertContainsFormValue($body, 'exportPlaceholders', '1');
        }

        if ($exportNotes) {
            $this->assertContainsFormValue($body, 'exportNotes', '1');
        }

        if ($exportNotesPages) {
            $this->assertContainsFormValue($body, 'exportNotesPages', '1');
        }

        if ($exportOnlyNotesPages) {
            $this->assertContainsFormValue($body, 'exportOnlyNotesPages', '1');
        }

        if ($exportNotesInMargin) {
            $this->assertContainsFormValue($body, 'exportNotesInMargin', '1');
        }

        if ($convertOooTargetToPdfTarget) {
            $this->assertContainsFormValue($body, 'convertOooTargetToPdfTarget', '1');
        }

        if ($exportLinksRelativeFsys) {
            $this->assertContainsFormValue($body, 'exportLinksRelativeFsys', '1');
        }

        if ($exportHiddenSlides) {
            $this->assertContainsFormValue($body, 'exportHiddenSlides', '1');
        }

        if ($skipEmptyPages) {
            $this->assertContainsFormValue($body, 'skipEmptyPages', '1');
        }

        if ($addOriginalDocumentAsStream) {
            $this->assertContainsFormValue($body, 'addOriginalDocumentAsStream', '1');
        }

        if ($singlePageSheets) {
            $this->assertContainsFormValue($body, 'singlePageSheets', '1');
        }

        if ($losslessImageCompression) {
            $this->assertContainsFormValue($body, 'losslessImageCompression', '1');
        }

        if ($quality !== null) {
            $this->assertContainsFormValue($body, 'quality', (string) $quality);
        }

        if ($reduceImageResolution) {
            $this->assertContainsFormValue($body, 'reduceImageResolution', '1');
        }

        if ($maxImageResolution !== null) {
            $this->assertContainsFormValue($body, 'maxImageResolution', (string) $maxImageResolution);
        }

        if ($nativeWatermarkText !== null) {
            $this->assertContainsFormValue($body, 'nativeWatermarkText', $nativeWatermarkText);
        }

        if ($nativeWatermarkColor !== null) {
            $this->assertContainsFormValue($body, 'nativeWatermarkColor', (string) $nativeWatermarkColor);
        }

        if ($nativeWatermarkFontHeight !== null) {
            $this->assertContainsFormValue($body, 'nativeWatermarkFontHeight', (string) $nativeWatermarkFontHeight);
        }

        if ($nativeWatermarkRotateAngle !== null) {
            $this->assertContainsFormValue($body, 'nativeWatermarkRotateAngle', (string) $nativeWatermarkRotateAngle);
        }

        if ($nativeWatermarkFontName !== null) {
            $this->assertContainsFormValue($body, 'nativeWatermarkFontName', $nativeWatermarkFontName);
        }

        if ($nativeTiledWatermarkText !== null) {
            $this->assertContainsFormValue($body, 'nativeTiledWatermarkText', $nativeTiledWatermarkText);
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

        if ($merge) {
            $this->assertContainsFormValue($body, 'merge', '1');
        }

        if ($flatten) {
            $this->assertContainsFormValue($body, 'flatten', '1');
        }

        if ($userPassword !== '') {
            $this->assertContainsFormValue($body, 'userPassword', $userPassword);
            $this->assertContainsFormValue($body, 'ownerPassword', $ownerPassword);
        }

        if (count($metadata) > 0) {
            $json = json_encode($metadata);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            $this->assertContainsFormValue($body, 'metadata', $json);
        }

        foreach ($files as $file) {
            $filename = $merge ? 'foo_' . $file->getFilename() : $file->getFilename();
            $file->getStream()->rewind();

            $this->assertContainsFormFile(
                $body,
                $filename,
                $file->getStream()->getContents(),
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            );
        }

        foreach ($embeds as $embed) {
            $embed->getStream()->rewind();
            $this->assertContainsFormFile(
                $body,
                $embed->getFilename(),
                $embed->getStream()->getContents(),
                'application/xml',
                'embeds',
            );
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

        if ($rotatePages === '') {
            return;
        }

        $this->assertContainsFormValue($body, 'rotatePages', $rotatePages);
    }

    /**
     * @return array<string, array{
     * files: array<int, Stream>,
     * password?: string|null,
     * landscape?: bool,
     * nativePageRanges?: string|null,
     * updateIndexes?: bool|null,
     * exportFormFields?: bool|null,
     * allowDuplicateFieldNames?: bool,
     * exportBookmarks?: bool|null,
     * exportBookmarksToPdfDestination?: bool,
     * exportPlaceholders?: bool,
     * exportNotes?: bool,
     * exportNotesPages?: bool,
     * exportOnlyNotesPages?: bool,
     * exportNotesInMargin?: bool,
     * convertOooTargetToPdfTarget?: bool,
     * exportLinksRelativeFsys?: bool,
     * exportHiddenSlides?: bool,
     * skipEmptyPages?: bool,
     * addOriginalDocumentAsStream?: bool,
     * singlePageSheets?: bool,
     * losslessImageCompression?: bool,
     * quality?: int|null,
     * reduceImageResolution?: bool,
     * maxImageResolution?: int|null,
     * splitMode?: SplitMode|null,
     * pdfa?: string|null,
     * pdfua?: bool,
     * metadata?: array<string, string|bool|float|int|array<string>>,
     * merge?: bool,
     * nativeWatermarkText?: string|null,
     * nativeWatermarkColor?: int|null,
     * nativeWatermarkFontHeight?: int|null,
     * nativeWatermarkRotateAngle?: int|null,
     * nativeWatermarkFontName?: string|null,
     * nativeTiledWatermarkText?: string|null,
     * flatten?: bool,
     * userPassword?: string,
     * ownerPassword?: string,
     * embeds?: array<int, Stream>,
     * watermarkSource?: string,
     * watermarkExpression?: string,
     * watermarkPages?: string,
     * watermarkOptions?: array<string, string>,
     * stampSource?: string,
     * stampExpression?: string,
     * stampPages?: string,
     * stampOptions?: array<string, string>,
     * rotateAngle?: int,
     * rotatePages?: string
     * }>
     */
    public static function provideConvertData(): array
    {
        return [
            'minimal' => [
                'files' => [
                    Stream::string('my.docx', 'Word content'),
                ],
            ],
            'full_options' => [
                'files' => [
                    Stream::string('my.docx', 'Word content'),
                    Stream::string('my_second.docx', 'Second Word content'),
                ],
                'password' => 'foo',
                'landscape' => true,
                'nativePageRanges' => '1-2',
                'updateIndexes' => false,
                'exportFormFields' => false,
                'allowDuplicateFieldNames' => true,
                'exportBookmarks' => false,
                'exportBookmarksToPdfDestination' => true,
                'exportPlaceholders' => true,
                'exportNotes' => true,
                'exportNotesPages' => true,
                'exportOnlyNotesPages' => true,
                'exportNotesInMargin' => true,
                'convertOooTargetToPdfTarget' => true,
                'exportLinksRelativeFsys' => true,
                'exportHiddenSlides' => true,
                'skipEmptyPages' => true,
                'addOriginalDocumentAsStream' => true,
                'singlePageSheets' => true,
                'losslessImageCompression' => true,
                'quality' => 100,
                'reduceImageResolution' => true,
                'maxImageResolution' => 150,
                'splitMode' => SplitMode::intervals(1),
                'pdfa' => 'PDF/A-1a',
                'pdfua' => true,
                'metadata' => ['Producer' => 'Gotenberg'],
                'merge' => true,
                'nativeWatermarkText' => 'DRAFT',
                'nativeWatermarkColor' => 16711680,
                'nativeWatermarkFontHeight' => 72,
                'nativeWatermarkRotateAngle' => 45,
                'nativeWatermarkFontName' => 'Arial',
                'nativeTiledWatermarkText' => 'CONFIDENTIAL',
                'flatten' => true,
                'userPassword' => 'my_user_password',
                'ownerPassword' => 'my_owner_password',
                'embeds' => [
                    Stream::string('my.xml', 'XML content'),
                    Stream::string('my_second.xml', 'Second XML content'),
                ],
                'watermarkSource' => 'my_watermark_source',
                'watermarkExpression' => 'my_watermark_expression',
                'watermarkPages' => '1-2',
                'watermarkOptions' => ['key' => 'value'],
                'stampSource' => 'my_stamp_source',
                'stampExpression' => 'my_stamp_expression',
                'stampPages' => '3-4',
                'stampOptions' => ['key' => 'value'],
                'rotateAngle' => 90,
                'rotatePages' => '1-3',
            ],
        ];
    }
}
