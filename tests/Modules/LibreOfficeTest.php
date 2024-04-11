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
        bool $singlePageSheets = false,
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

        if ($singlePageSheets) {
            $libreOffice->singlePageSheets();
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
        expect($body)->unless($singlePageSheets === false, fn ($body) => $body->toContainFormValue('singlePageSheets', '1'));
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
        'PDF/A-1a',
        true,
        [ 'Producer' => 'Gotenberg' ],
        true,
    ],
]);
