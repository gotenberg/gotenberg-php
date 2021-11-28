<?php

declare(strict_types=1);

use Gotenberg\Modules\ChromiumExtraLinkTag;
use Gotenberg\Stream;

it(
    'creates an extra link tag from a URL',
    function (): void {
        $linkTag = ChromiumExtraLinkTag::url('https://my.css');

        expect($linkTag->getHref())->toEqual('https://my.css');
        expect($linkTag->getStream())->toBeNull();
    }
);

it(
    'creates an extra link tag from a stream',
    function (): void {
        $stream  = Stream::string('my.css', 'CSS Content');
        $linkTag = ChromiumExtraLinkTag::stream($stream);

        expect($linkTag->getHref())->toEqual('my.css');
        expect($linkTag->getStream())->not()->toBeNull();
    }
);
