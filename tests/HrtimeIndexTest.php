<?php

declare(strict_types=1);

use Gotenberg\HrtimeIndex;

it(
    'creates alphabetical ordered indexes',
    function (): void {
        $index   = new HrtimeIndex();
        $indexes = [];

        for ($i = 0; $i < 100; $i++) {
            $indexes[$i] = $index->create();

            if ($i === 0) {
                continue;
            }

            $result = $indexes[$i] > $indexes[$i - 1];
            expect($result)->toBeTrue();
        }
    },
);
