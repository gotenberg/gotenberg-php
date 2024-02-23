<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

class ChromiumPdf
{
    use ChromiumMultipartFormDataModule;

    /**
     * Defines whether to print the entire content in one single page.
     */
    public function singlePage(): self
    {
        $this->formValue('singlePage', true);

        return $this;
    }

    /**
     * Overrides the default paper size, in inches.
     *
     * Examples of paper size (width x height):
     *
     * Letter - 8.5 x 11 (default)
     * Legal - 8.5 x 14
     * Tabloid - 11 x 17
     * Ledger - 17 x 11
     * A0 - 33.1 x 46.8
     * A1 - 23.4 x 33.1
     * A2 - 16.54 x 23.4
     * A3 - 11.7 x 16.54
     * A4 - 8.27 x 11.7
     * A5 - 5.83 x 8.27
     * A6 - 4.13 x 5.83
     */
    public function paperSize(float $width, float $height): self
    {
        $this->formValue('paperWidth', $width);
        $this->formValue('paperHeight', $height);

        return $this;
    }

    /**
     * Overrides the default margins (i.e., 0.39), in inches.
     */
    public function margins(float $top, float $bottom, float $left, float $right): self
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
    public function preferCssPageSize(): self
    {
        $this->formValue('preferCssPageSize', true);

        return $this;
    }

    /**
     * Prints the background graphics.
     */
    public function printBackground(): self
    {
        $this->formValue('printBackground', true);

        return $this;
    }

    /**
     * Sets the paper orientation to landscape.
     */
    public function landscape(): self
    {
        $this->formValue('landscape', true);

        return $this;
    }

    /**
     * Overrides the default scale of the page rendering (i.e., 1.0).
     */
    public function scale(float $scale): self
    {
        $this->formValue('scale', $scale);

        return $this;
    }

    /**
     * Set the page ranges to print, e.g., "1-5, 8, 11-13".
     * Empty means all pages.
     */
    public function nativePageRanges(string $ranges): self
    {
        $this->formValue('nativePageRanges', $ranges);

        return $this;
    }

    /**
     * Adds a header to each page.
     *
     * Note: it automatically sets the filename to "header.html", as required
     * by Gotenberg.
     */
    public function header(Stream $header): self
    {
        $this->formFile('header.html', $header->getStream());

        return $this;
    }

    /**
     * Adds a footer to each page.
     *
     * Note: it automatically sets the filename to "footer.html", as required
     * by Gotenberg.
     */
    public function footer(Stream $footer): self
    {
        $this->formFile('footer.html', $footer->getStream());

        return $this;
    }

    /**
     * Sets the PDF/A format of the resulting PDF.
     */
    public function pdfa(string $format): self
    {
        $this->formValue('pdfa', $format);

        return $this;
    }

    /**
     * Enables PDF for Universal Access for optimal accessibility.
     */
    public function pdfua(): self
    {
        $this->formValue('pdfua', true);

        return $this;
    }

    /**
     * Converts a target URL to PDF.
     *
     * @throws NativeFunctionErrored
     */
    public function url(string $url): RequestInterface
    {
        $this->formValue('url', $url);
        $this->endpoint = '/forms/chromium/convert/url';

        return $this->request();
    }

    /**
     * Converts an HTML document to PDF.
     *
     * Note: it automatically sets the index filename to "index.html", as
     * required by Gotenberg.
     */
    public function html(Stream $index): RequestInterface
    {
        $this->formFile('index.html', $index->getStream());
        $this->endpoint = '/forms/chromium/convert/html';

        return $this->request();
    }

    /**
     * Converts one or more markdown files to PDF.
     *
     * Note: it automatically sets the index filename to "index.html", as
     * required by Gotenberg.
     */
    public function markdown(Stream $index, Stream $markdown, Stream ...$markdowns): RequestInterface
    {
        $this->formFile('index.html', $index->getStream());
        $this->formFile($markdown->getFilename(), $markdown->getStream());

        foreach ($markdowns as $markdown) {
            $this->formFile($markdown->getFilename(), $markdown->getStream());
        }

        $this->endpoint = '/forms/chromium/convert/markdown';

        return $this->request();
    }
}
