<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

class ChromiumScreenshot
{
    use ChromiumMultipartFormDataModule;

    /**
     * The device screen width in pixels.
     */
    public function width(int $width): self
    {
        $this->formValue('width', $width);

        return $this;
    }

    /**
     * The device screen height in pixels.
     */
    public function height(int $height): self
    {
        $this->formValue('height', $height);

        return $this;
    }

    /**
     * Defines whether to clip the screenshot according to the device
     * dimensions.
     */
    public function clip(): self
    {
        $this->formValue('clip', true);

        return $this;
    }

    /**
     * PNG as image compression format.
     */
    public function png(): self
    {
        $this->formValue('format', 'png');

        return $this;
    }

    /**
     * JPEG as image compression format.
     */
    public function jpeg(): self
    {
        $this->formValue('format', 'jpeg');

        return $this;
    }

    /**
     * WEBP as image compression format.
     */
    public function webp(): self
    {
        $this->formValue('format', 'webp');

        return $this;
    }

    /**
     * The compression quality from range 0 to 100 (jpeg only).
     */
    public function quality(int $quality): self
    {
        $this->formValue('quality', $quality);

        return $this;
    }

    /**
     * Defines whether to optimize image encoding for speed, not for resulting
     * size.
     */
    public function optimizeForSpeed(): self
    {
        $this->formValue('optimizeForSpeed', true);

        return $this;
    }

    /**
     * Captures a screenshot of a target URL.
     *
     * @throws NativeFunctionErrored
     */
    public function url(string $url): RequestInterface
    {
        $this->formValue('url', $url);
        $this->endpoint = '/forms/chromium/screenshot/url';

        return $this->request();
    }

    /**
     * Captures a screenshot of an HTML document.
     *
     * Note: it automatically sets the index filename to "index.html", as
     * required by Gotenberg.
     */
    public function html(Stream|null $index): RequestInterface
    {
        if ($index !== null) {
            $this->formFile('index.html', $index->getStream());
        }

        $this->endpoint = '/forms/chromium/screenshot/html';

        return $this->request();
    }

    /**
     * Captures a screenshot of one or more markdown files.
     *
     * Note: it automatically sets the index filename to "index.html", as
     * required by Gotenberg.
     */
    public function markdown(Stream|null $index, Stream ...$markdowns): RequestInterface
    {
        if ($index !== null) {
            $this->formFile('index.html', $index->getStream());
        }

        foreach ($markdowns as $markdown) {
            $this->formFile($markdown->getFilename(), $markdown->getStream());
        }

        $this->endpoint = '/forms/chromium/screenshot/markdown';

        return $this->request();
    }
}
