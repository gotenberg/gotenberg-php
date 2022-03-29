<?php

declare(strict_types=1);

use Gotenberg\Gotenberg;
use Gotenberg\Index;
use Gotenberg\Stream;

it(
    'creates a valid request for the "/forms/libreoffice/convert" endpoint',
    /**
     * @param Stream[] $files
     */
    function (
        array $files,
        bool $landscape = false,
        ?string $nativePageRanges = null,
        bool $nativePdfA1aFormat = false,
        ?string $nativePdfFormat = null,
        ?string $pdfFormat = null,
        bool $merge = false
    ): void {
        $libreOffice = Gotenberg::libreOffice('');

        if ($landscape) {
            $libreOffice->landscape();
        }

        if ($nativePageRanges !== null) {
            $libreOffice->nativePageRanges($nativePageRanges);
        }

        if ($nativePdfA1aFormat) {
            $libreOffice->nativePdfA1aFormat();
        }

        if ($nativePdfFormat !== null) {
            $libreOffice->nativePdfFormat($nativePdfFormat);
        }

        if ($pdfFormat !== null) {
            $libreOffice->pdfFormat($pdfFormat);
        }

        if ($merge) {
            $libreOffice->merge();
        }

        $request = $libreOffice->convert(...$files);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/libreoffice/convert');
        expect($body)->unless($landscape === false, fn ($body) => $body->toContainFormValue('landscape', '1'));
        expect($body)->unless($nativePageRanges === null, fn ($body) => $body->toContainFormValue('nativePageRanges', $nativePageRanges));
        expect($body)->unless($nativePdfA1aFormat === false, fn ($body) => $body->toContainFormValue('nativePdfA1aFormat', '1'));
        expect($body)->unless($nativePdfFormat === null, fn ($body) => $body->toContainFormValue('nativePdfFormat', $nativePdfFormat));
        expect($body)->unless($pdfFormat === null, fn ($body) => $body->toContainFormValue('pdfFormat', $pdfFormat));
        expect($body)->unless($merge === false, fn ($body) => $body->toContainFormValue('merge', '1'));

        foreach ($files as $index => $file) {
            $filename = $merge ? Index::toAlpha($index + 1) . '_' . $file->getFilename() : $file->getFilename();
            $file->getStream()->rewind();

            expect($body)->toContainFormFile($filename, $file->getStream()->getContents(), 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        }
    }
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
        true,
        'PDF/A-1a',
        'PDF/A-1a',
        true,
    ],
]);
