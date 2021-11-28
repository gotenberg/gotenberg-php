<?php

declare(strict_types=1);

use Gotenberg\Modules\ChromiumExtraScriptTag;
use Gotenberg\Stream;

it(
    'creates an extra script tag from a URL',
    function (): void {
        $scriptTag = ChromiumExtraScriptTag::url('https://my.js');

        expect($scriptTag->getSrc())->toEqual('https://my.js');
        expect($scriptTag->getStream())->toBeNull();
    }
);

it(
    'creates an extra script tag from a stream',
    function (): void {
        $stream    = Stream::string('my.js', 'JavaScript content');
        $scriptTag = ChromiumExtraScriptTag::stream($stream);

        expect($scriptTag->getSrc())->toEqual('my.js');
        expect($scriptTag->getStream())->toBe($stream);
    }
);
