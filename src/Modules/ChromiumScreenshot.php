<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Psr\Http\Message\RequestInterface;

class ChromiumScreenshot
{
    use ChromiumMultipartFormDataModule;

    /**
     * The device screen width in pixels.
     */
    public function width(int $width): static
    {
        $this->formValue('width', $width);

        return $this;
    }

    /**
     * The device screen height in pixels.
     */
    public function height(int $height): static
    {
        $this->formValue('height', $height);

        return $this;
    }

    /**
     * Clips the screenshot to the device dimensions.
     */
    public function clip(): static
    {
        $this->formValue('clip', true);

        return $this;
    }

    /**
     * The device scale factor, which controls the screenshot pixel density.
     */
    public function deviceScaleFactor(float $factor): static
    {
        $this->formValue('deviceScaleFactor', $factor);

        return $this;
    }

    /**
     * PNG as image compression format.
     */
    public function png(): static
    {
        $this->formValue('format', 'png');

        return $this;
    }

    /**
     * JPEG as image compression format.
     */
    public function jpeg(): static
    {
        $this->formValue('format', 'jpeg');

        return $this;
    }

    /**
     * WEBP as image compression format.
     */
    public function webp(): static
    {
        $this->formValue('format', 'webp');

        return $this;
    }

    /**
     * The compression quality, from 1 to 100 (jpeg and webp only).
     */
    public function quality(int $quality): static
    {
        $this->formValue('quality', $quality);

        return $this;
    }

    /**
     * Optimizes image encoding for speed, not for resulting size.
     */
    public function optimizeForSpeed(): static
    {
        $this->formValue('optimizeForSpeed', true);

        return $this;
    }

    /**
     * Clips the screenshot to the bounding box of the first element matching
     * the given CSS selector.
     */
    public function selector(string $selector): static
    {
        $this->formValue('selector', $selector);

        return $this;
    }

    private function route(string $source): RequestInterface
    {
        $this->endpoint = '/forms/chromium/screenshot/' . $source;

        return $this->request();
    }
}
