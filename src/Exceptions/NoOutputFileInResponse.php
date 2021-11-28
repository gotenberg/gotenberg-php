<?php

declare(strict_types=1);

namespace Gotenberg\Exceptions;

use Exception;

final class NoOutputFileInResponse extends Exception
{
    public function __construct()
    {
        parent::__construct('No file in the Gotenberg API response');
    }
}
