<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\PaperSize;
use Gotenberg\StagesModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

class ChromiumPdf
{
    use ChromiumMultipartFormDataModule;
    use StagesModule;

    /**
     * Prints the entire content in one single page, as tall as the content.
     */
    public function singlePage(): static
    {
        $this->formValue('singlePage', true);

        return $this;
    }

    /**
     * Sets the paper size; default PaperSize::Letter. paperDimensions()
     * overrides it.
     */
    public function paperSize(PaperSize $size): static
    {
        $this->formValue('paperSize', $size->value);

        return $this;
    }

    /**
     * Overrides the paper width and height, using various units such as 72pt,
     * 96px, 1in, 25.4mm, 2.54cm, or 6pc. The default unit is inches when
     * not specified.
     */
    public function paperDimensions(float|string $width, float|string $height): static
    {
        $this->formValue('paperWidth', $width);
        $this->formValue('paperHeight', $height);

        return $this;
    }

    /**
     * Overrides the default margins (i.e., 0.39), using various units such as
     * 72pt, 96px, 1in, 25.4mm, 2.54cm, or 6pc. The default unit is inches when
     * not specified.
     */
    public function margins(float|string $top, float|string $bottom, float|string $left, float|string $right): static
    {
        $this->formValue('marginTop', $top);
        $this->formValue('marginBottom', $bottom);
        $this->formValue('marginLeft', $left);
        $this->formValue('marginRight', $right);

        return $this;
    }

    /**
     * Forces page size as defined by CSS.
     */
    public function preferCssPageSize(): static
    {
        $this->formValue('preferCssPageSize', true);

        return $this;
    }

    /**
     * Embeds the document outline, made from the headings, as bookmarks.
     */
    public function nativeBookmarks(): static
    {
        $this->formValue('nativeBookmarks', true);

        return $this;
    }

    /**
     * Generates a tagged (accessible) PDF.
     */
    public function nativeTaggedPdf(): static
    {
        $this->formValue('nativeTaggedPdf', true);

        return $this;
    }

    /**
     * Prints the background graphics.
     */
    public function printBackground(): static
    {
        $this->formValue('printBackground', true);

        return $this;
    }

    /**
     * Sets the paper orientation to landscape.
     */
    public function landscape(): static
    {
        $this->formValue('landscape', true);

        return $this;
    }

    /**
     * Overrides the default scale of the page rendering (i.e., 1.0).
     */
    public function scale(float $scale): static
    {
        $this->formValue('scale', $scale);

        return $this;
    }

    /**
     * Sets the pages to print, in Chromium's syntax, e.g., "1-5, 8, 11-13".
     * The select stage picks pages after printing.
     */
    public function nativePages(string $pages): static
    {
        $this->formValue('nativePages', $pages);

        return $this;
    }

    /**
     * Adds a header to each page.
     *
     * Note: it automatically sets the filename to "header.html", as required
     * by Gotenberg.
     */
    public function header(Stream $header): static
    {
        $this->formFile($header, filename: 'header.html');

        return $this;
    }

    /**
     * Adds a footer to each page.
     *
     * Note: it automatically sets the filename to "footer.html", as required
     * by Gotenberg.
     */
    public function footer(Stream $footer): static
    {
        $this->formFile($footer, filename: 'footer.html');

        return $this;
    }

    private function route(string $source): RequestInterface
    {
        $this->endpoint = '/forms/chromium/convert/' . $source;

        return $this->request();
    }
}
