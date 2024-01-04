<?php

declare(strict_types=1);

use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Gotenberg\Gotenberg;
use Gotenberg\Test\DummyClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

it(
    'sends a request',
    function (): void {
        $response = new Response(200, ['Gotenberg-Trace' => 'debug']);
        $client   = new DummyClient($response);

        $response = Gotenberg::send(new Request('POST', 'https://my.url'), $client);

        expect($response)->not()->toBeNull();
    },
);

it(
    'sends a request and throws an exception if response is not 2xx',
    function (bool $withTrace): void {
        $response = new Response(400, $withTrace ? ['Gotenberg-Trace' => 'debug'] : [], 'Bad Request');
        $client   = new DummyClient($response);

        try {
            Gotenberg::send(new Request('POST', 'https://my.url'), $client);
        } catch (GotenbergApiErrored $e) {
            expect($e->getCode())->toEqual(400);
            expect($e->getMessage())->toEqual('Bad Request');
            expect($e->getGotenbergTrace())->toEqual($withTrace ? 'debug' : '');
            expect($e->getResponse())->toBe($response);

            throw $e;
        }
    },
)->with([
    'with trace' => [ true ],
    'without trace' => [ false ],
])->throws(GotenbergApiErrored::class);

it(
    'saves the output file',
    function (): void {
        $response = new Response(200, ['Content-Disposition' => 'attachment; filename=my.pdf']);
        $client   = new DummyClient($response);

        $filename = Gotenberg::save(new Request('POST', 'https://my.url'), sys_get_temp_dir(), $client);

        expect(unlink(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'my.pdf'))->toBeTrue();
        expect($filename)->toEqual('my.pdf');
    },
);

it(
    'throws an exception if there is no attachment',
    function (string|null $contentDisposition): void {
        $response = new Response(200, $contentDisposition === null ? [] : ['Content-Disposition' => $contentDisposition]);
        $client   = new DummyClient($response);

        Gotenberg::save(new Request('POST', 'https://my.url'), sys_get_temp_dir(), $client);
    },
)->with([
    'without content disposition' => [ null ],
    'with content disposition' => [ 'no attachment' ],
])->throws(NoOutputFileInResponse::class);
