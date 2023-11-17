<?php

declare(strict_types=1);

use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Gotenberg\Test\DummyIndex;

it(
    'creates a valid request for the "/forms/pdfengines/merge" endpoint',
    /**
     * @param Stream[] $pdfs
     */
    function (array $pdfs, ?string $pdfFormat = null, ?string $pdfa = null, bool $pdfua = false): void {
        $pdfEngines = Gotenberg::pdfEngines('')->index(new DummyIndex());

        if ($pdfFormat !== null) {
            $pdfEngines->pdfFormat($pdfFormat);
        }

        if ($pdfa !== null) {
            $pdfEngines->pdfa($pdfa);
        }

        if ($pdfua) {
            $pdfEngines->pdfua();
        }

        $request = $pdfEngines->merge(...$pdfs);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/pdfengines/merge');

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            expect($body)->toContainFormFile('foo_' . $pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }

        expect($body)->unless($pdfFormat === null, fn ($body) => $body->toContainFormValue('pdfFormat', $pdfFormat));
    }
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
        'PDF/A-1a',
        true,
    ],
]);

it(
    'creates a valid request for the "/forms/pdfengines/convert" endpoint',
    function (string $pdfa, Stream ...$pdfs): void {
        $request = Gotenberg::pdfEngines('')->convert($pdfa, ...$pdfs);
        $body    = sanitize($request->getBody()->getContents());

        expect($request->getUri()->getPath())->toBe('/forms/pdfengines/convert');
        expect($body)->toContainFormValue('pdfFormat', $pdfa);
        expect($body)->toContainFormValue('pdfa', $pdfa);

        foreach ($pdfs as $pdf) {
            $pdf->getStream()->rewind();
            expect($body)->toContainFormFile($pdf->getFilename(), $pdf->getStream()->getContents(), 'application/pdf');
        }
    }
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
