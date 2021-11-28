<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\MultipartFormDataModule;
use Psr\Http\Message\RequestInterface;

class DummyMultipartFormDataModule
{
    use MultipartFormDataModule;

    public function build(): RequestInterface
    {
        $this->endpoint = '/foo';

        return $this->request();
    }
}
