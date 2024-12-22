<?php

declare(strict_types=1);

use Gotenberg\SplitMode;

it(
    'creates an intervals split mode',
    function (): void {
        $mode = SplitMode::intervals(1);
        expect($mode->mode)->toBe('intervals');
        expect($mode->span)->toBe('1');
        expect($mode->unify)->toBeFalse();
    },
);

it(
    'creates a pages split mode',
    function (): void {
        $mode = SplitMode::pages('1-2', true);
        expect($mode->mode)->toBe('pages');
        expect($mode->span)->toBe('1-2');
        expect($mode->unify)->toBeTrue();
    },
);
