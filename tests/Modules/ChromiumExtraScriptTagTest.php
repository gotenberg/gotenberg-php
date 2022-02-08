<?php

declare(strict_types=1);

use Gotenberg\Modules\ChromiumExtraScriptTag;

it(
    'creates an extra script tag from a URL',
    function (): void {
        $linkTag = new ChromiumExtraScriptTag('https://my.js');

        expect($linkTag->getSrc())->toEqual('https://my.js');
    }
);
