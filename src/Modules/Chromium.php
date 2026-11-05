<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

final readonly class Chromium
{
    public function __construct(public string $baseUrl)
    {
    }

    public function pdf(): ChromiumPdf
    {
        return new ChromiumPdf($this->baseUrl);
    }

    public function screenshot(): ChromiumScreenshot
    {
        return new ChromiumScreenshot($this->baseUrl);
    }
}
