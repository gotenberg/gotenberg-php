<?php

declare(strict_types=1);

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Gotenberg\Test\DummyIndex;

it(
    'creates a valid request for the "/forms/libreoffice/convert" endpoint',
    /**
     * @param Stream[] $files
     * @param array<string,string|bool|float|int|array<string>> $metadata
     */
    function (
        array $files,
        bool $landscape = false,
        string|null $nativePageRanges = null,
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
        string|null $pdfa = null,
        bool $pdfua = false,
        array $metadata = [],
        bool $merge = false,
    ): void {
        $libreOffice = Gotenberg::libreOffice('');

        if ($landscape) {
            $libreOffice->landscape();
        }

        if ($nativePageRanges !== null) {
            $libreOffice->nativePageRanges($nativePageRanges);
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

        if ($pdfa !== null) {
            $libreOffice->pdfa($pdfa);
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

        $request = $libreOffice->convert(...$files);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/libreoffice/convert');
        expect($body)->unless($landscape === false, fn ($body) => $body->toContainFormValue('landscape', '1'));
        expect($body)->unless($nativePageRanges === null, fn ($body) => $body->toContainFormValue('nativePageRanges', $nativePageRanges));
        expect($body)->unless($exportFormFields === null, fn ($body) => $body->toContainFormValue('exportFormFields', $exportFormFields === true ? '1' : '0'));
        expect($body)->unless($allowDuplicateFieldNames === false, fn ($body) => $body->toContainFormValue('allowDuplicateFieldNames', '1'));
        expect($body)->unless($exportBookmarks === null, fn ($body) => $body->toContainFormValue('exportBookmarks', $exportBookmarks === true ? '1' : '0'));
        expect($body)->unless($exportBookmarksToPdfDestination === false, fn ($body) => $body->toContainFormValue('exportBookmarksToPdfDestination', '1'));
        expect($body)->unless($exportPlaceholders === false, fn ($body) => $body->toContainFormValue('exportPlaceholders', '1'));
        expect($body)->unless($exportNotes === false, fn ($body) => $body->toContainFormValue('exportNotes', '1'));
        expect($body)->unless($exportNotesPages === false, fn ($body) => $body->toContainFormValue('exportNotesPages', '1'));
        expect($body)->unless($exportOnlyNotesPages === false, fn ($body) => $body->toContainFormValue('exportOnlyNotesPages', '1'));
        expect($body)->unless($exportNotesInMargin === false, fn ($body) => $body->toContainFormValue('exportNotesInMargin', '1'));
        expect($body)->unless($convertOooTargetToPdfTarget === false, fn ($body) => $body->toContainFormValue('convertOooTargetToPdfTarget', '1'));
        expect($body)->unless($exportLinksRelativeFsys === false, fn ($body) => $body->toContainFormValue('exportLinksRelativeFsys', '1'));
        expect($body)->unless($exportHiddenSlides === false, fn ($body) => $body->toContainFormValue('exportHiddenSlides', '1'));
        expect($body)->unless($skipEmptyPages === false, fn ($body) => $body->toContainFormValue('skipEmptyPages', '1'));
        expect($body)->unless($addOriginalDocumentAsStream === false, fn ($body) => $body->toContainFormValue('addOriginalDocumentAsStream', '1'));
        expect($body)->unless($singlePageSheets === false, fn ($body) => $body->toContainFormValue('singlePageSheets', '1'));
        expect($body)->unless($losslessImageCompression === false, fn ($body) => $body->toContainFormValue('losslessImageCompression', '1'));
        expect($body)->unless($quality === null, fn ($body) => $body->toContainFormValue('quality', $quality));
        expect($body)->unless($reduceImageResolution === false, fn ($body) => $body->toContainFormValue('reduceImageResolution', '1'));
        expect($body)->unless($maxImageResolution === null, fn ($body) => $body->toContainFormValue('maxImageResolution', $maxImageResolution));
        expect($body)->unless($pdfa === null, fn ($body) => $body->toContainFormValue('pdfa', $pdfa));
        expect($body)->unless($pdfua === false, fn ($body) => $body->toContainFormValue('pdfua', '1'));
        expect($body)->unless($merge === false, fn ($body) => $body->toContainFormValue('merge', '1'));

        if (count($metadata) > 0) {
            $json = json_encode($metadata);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            expect($body)->toContainFormValue('metadata', $json);
        }

        foreach ($files as $file) {
            $filename = $merge ? 'foo_' . $file->getFilename() : $file->getFilename();
            $file->getStream()->rewind();

            expect($body)->toContainFormFile($filename, $file->getStream()->getContents(), 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        }
    },
)->with([
    [
        [
            Stream::string('my.docx', 'Word content'),
        ],
    ],
    [
        [
            Stream::string('my.docx', 'Word content'),
            Stream::string('my_second.docx', 'Second Word content'),
        ],
        true,
        '1-2',
        false,
        true,
        false,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        100,
        true,
        150,
        'PDF/A-1a',
        true,
        [ 'Producer' => 'Gotenberg' ],
        true,
    ],
]);
