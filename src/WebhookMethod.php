<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The HTTP methods Gotenberg uploads a result to a webhook with.
 */
enum WebhookMethod: string
{
    case Post  = 'POST';
    case Put   = 'PUT';
    case Patch = 'PATCH';
}
