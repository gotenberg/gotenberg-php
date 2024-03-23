<?php

declare(strict_types=1);

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Gotenberg\Test\DummyIndex;

it(
    'creates a valid request for the "/forms/pdfengines/merge" endpoint',
    /**
     * @param Stream[] $pdfs
     * @param array<string,string|bool|float|int|array<string>> $metadata
     */
    function (array $pdfs, string|null $pdfa = null, bool $pdfua = false, array $metadata = []): void {
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

        $request = $pdfEngines->merge(...$pdfs);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/pdfengines/merge');
        expect($body)->unless($pdfa === null, fn ($body) => $body->toContainFormValue('pdfa', $pdfa));
        expect($body)->unless($pdfua === false, fn ($body) => $body->toContainFormValue('pdfa', $pdfa));

        if (count($metadata) > 0) {
            $json = json_encode($metadata);
            if ($json === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }

            expect($body)->toContainFormValue('metadata', $json);
        }

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            expect($body)->toContainFormFile('foo_' . $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    },
)->with([
    [
        [
            Stream::string('my.pdf', 'PDF content'),
            Stream::string('my_second.pdf', 'Second PDF content'),
        ],
    ],
    [
        [
            Stream::string('my.pdf', 'PDF content'),
            Stream::string('my_second.pdf', 'Second PDF content'),
            Stream::string('my_third.pdf', 'Third PDF content'),
        ],
        'PDF/A-1a',
        true,
        [ 'Producer' => 'Gotenberg' ],
    ],
]);

it(
    'creates a valid request for the "/forms/pdfengines/convert" endpoint',
    function (string $pdfa, Stream ...$pdfs): void {
        $request = Gotenberg::pdfEngines('')->convert($pdfa, ...$pdfs);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/pdfengines/convert');
        expect($body)->toContainFormValue('pdfa', $pdfa);

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            expect($body)->toContainFormFile($pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    },
)->with([
    [
        'PDF/A-1a',
        Stream::string('my.pdf', 'PDF content'),
    ],
    [
        'PDF/A-1a',
        Stream::string('my.pdf', 'PDF content'),
        Stream::string('my_second.pdf', 'Second PDF content'),
    ],
]);

it(
    'creates a valid request for the "/forms/pdfengines/metadata/read" endpoint',
    /** @param Stream[] $pdfs */
    function (array $pdfs): void {
        $pdfEngines = Gotenberg::pdfEngines('');

        $request = $pdfEngines->readMetadata(...$pdfs);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/pdfengines/metadata/read');

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            expect($body)->toContainFormFile($pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    },
)->with([
    [
        [
            Stream::string('my.pdf', 'PDF content'),
            Stream::string('my_second.pdf', 'Second PDF content'),
        ],
    ],
]);

it(
    'creates a valid request for the "/forms/pdfengines/metadata/write" endpoint',
    /**
     * @param array<string,string|bool|float|int|array<string>> $metadata
     * @param Stream[] $pdfs
     */
    function (array $metadata, array $pdfs): void {
        $pdfEngines = Gotenberg::pdfEngines('');

        $request = $pdfEngines->writeMetadata($metadata, ...$pdfs);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/pdfengines/metadata/write');

        $json = json_encode($metadata);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        expect($body)->toContainFormValue('metadata', $json);

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            expect($body)->toContainFormFile($pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    },
)->with([
    [
        [ 'Producer' => 'Gotenberg' ],
        [
            Stream::string('my.pdf', 'PDF content'),
            Stream::string('my_second.pdf', 'Second PDF content'),
        ],
    ],
]);
