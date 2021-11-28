<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\ApiModule;
use Psr\Http\Message\RequestInterface;

class DummyApiModule
{
    use ApiModule;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function build(): RequestInterface
    {
        $this->endpoint = '/foo';

        return $this->request('GET');
    }
}
