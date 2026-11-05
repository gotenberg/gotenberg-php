<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The Chromium console message types.
 */
enum ConsoleMessageType: string
{
    case Log     = 'log';
    case Debug   = 'debug';
    case Info    = 'info';
    case Warning = 'warning';
    case Error   = 'error';
    case Assert  = 'assert';
}
