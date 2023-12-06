<?php

declare(strict_types=1);

use Gotenberg\Stream;

it(
    'creates a stream from a string',
    function (): void {
        $stream = Stream::string('my.txt', 'My content');
        $stream->getStream()->rewind();

        expect($stream->getFilename())->toEqual('my.txt');
        expect($stream->getStream()->getContents())->toEqual('My content');
    },
);

it(
    'creates a stream from a path',
    function (): void {
        $stream = Stream::path(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'dummy.txt');

        expect($stream->getFilename())->toEqual('dummy.txt');
        expect($stream->getStream()->getContents())->toEqual('Dummy content');
    },
);
