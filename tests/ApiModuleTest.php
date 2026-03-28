<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\Test\Helpers\Dummies\DummyApiModule;
use PHPUnit\Framework\Attributes\Test;

final class ApiModuleTest extends TestCase
{
    #[Test]
    public function it_creates_a_valid_request_with_a_correlation_id_header(): void
    {
        $dummy   = new DummyApiModule('https://my.url/');
        $request = $dummy
            ->correlationId('debug')
            ->build();

        $this->assertSame('https://my.url', $dummy->getUrl());
        $this->assertSame(['debug'], $request->getHeader('Gotenberg-Trace'));
    }

    #[Test]
    public function it_creates_a_valid_request_with_a_deprecated_trace_header(): void
    {
        $dummy   = new DummyApiModule('https://my.url/');
        $request = $dummy
            ->trace('debug')
            ->build();

        $this->assertSame(['debug'], $request->getHeader('Gotenberg-Trace'));
    }
}
