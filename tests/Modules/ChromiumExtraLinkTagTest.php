<?php

declare(strict_types=1);

use Gotenberg\Modules\ChromiumExtraLinkTag;

it(
    'creates an extra link tag from a URL',
    function (): void {
        $linkTag = new ChromiumExtraLinkTag('https://my.css');

        expect($linkTag->getHref())->toEqual('https://my.css');
    }
);
