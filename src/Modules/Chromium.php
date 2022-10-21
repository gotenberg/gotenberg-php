<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\Exceptions\NativeFunctionErroed;
use Gotenberg\MultipartFormDataModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

use function count;
use function json_encode;

class Chromium
{
    use MultipartFormDataModule;

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
     * Hides default white background and allows generating PDFs with
     * transparency.
     */
    public function omitBackground(): self
    {
        $this->formValue('omitBackground', true);

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
     * Sets the duration (i.e., "1s", "2ms", etc.) to wait when loading an HTML
     * document before converting it to PDF.
     */
    public function waitDelay(string $delay): self
    {
        $this->formValue('waitDelay', $delay);

        return $this;
    }

    /**
     * @deprecated
     */
    public function waitWindowStatus(string $status): self
    {
        $this->formValue('waitWindowStatus', $status);

        return $this;
    }

    /**
     * Sets the JavaScript expression to wait before converting an HTML
     * document to PDF until it returns true.
     *
     * For instance: "window.status === 'ready'".
     */
    public function waitForExpression(string $expression): self
    {
        $this->formValue('waitForExpression', $expression);

        return $this;
    }

    /**
     * Overrides the default "User-Agent" header.
     */
    public function userAgent(string $userAgent): self
    {
        $this->formValue('userAgent', $userAgent);

        return $this;
    }

    /**
     * Sets extra HTTP headers that Chromium will send when loading the HTML
     * document.
     *
     * @param array<string,string> $headers
     *
     * @throws NativeFunctionErroed
     */
    public function extraHttpHeaders(array $headers): self
    {
        $json = json_encode($headers);
        if ($json === false) {
            throw NativeFunctionErroed::createFromLastPhpError();
        }

        $this->formValue('extraHttpHeaders', $json);

        return $this;
    }

    /**
     * Forces Gotenberg to return a 409 Conflict response if there are
     * exceptions in the Chromium console
     */
    public function failOnConsoleExceptions(): self
    {
        $this->formValue('failOnConsoleExceptions', true);

        return $this;
    }

    /**
     * Forces Chromium to emulate the media type "print".
     */
    public function emulatePrintMediaType(): self
    {
        $this->formValue('emulatedMediaType', 'print');

        return $this;
    }

    /**
     * Forces Chromium to emulate the media type "screen".
     */
    public function emulateScreenMediaType(): self
    {
        $this->formValue('emulatedMediaType', 'screen');

        return $this;
    }

    /**
     * Sets the PDF format of the resulting PDF.
     *
     * See https://gotenberg.dev/docs/modules/pdf-engines#engines.
     */
    public function pdfFormat(string $format): self
    {
        $this->formValue('pdfFormat', $format);

        return $this;
    }

    /**
     * Sets the additional files, like images, fonts, stylesheets, and so on.
     */
    public function assets(Stream ...$assets): self
    {
        foreach ($assets as $asset) {
            $this->formFile($asset->getFilename(), $asset->getStream());
        }

        return $this;
    }

    /**
     * Converts a target URL to PDF.
     *
     * See https://gotenberg.dev/docs/modules/chromium#url.
     *
     * @param ChromiumExtraLinkTag[]   $extraLinkTags
     * @param ChromiumExtraScriptTag[] $extraScriptTags
     *
     * @throws NativeFunctionErroed
     */
    public function url(string $url, array $extraLinkTags = [], array $extraScriptTags = []): RequestInterface
    {
        $this->formValue('url', $url);

        $links   = [];
        $scripts = [];

        foreach ($extraLinkTags as $linkTag) {
            $links[] = [
                'href' => $linkTag->getHref(),
            ];
        }

        foreach ($extraScriptTags as $scriptTag) {
            $scripts[] = [
                'src' => $scriptTag->getSrc(),
            ];
        }

        if (count($links) > 0) {
            $json = json_encode($links);
            if ($json === false) {
                throw NativeFunctionErroed::createFromLastPhpError();
            }

            $this->formValue('extraLinkTags', $json);
        }

        if (count($scripts) > 0) {
            $json = json_encode($scripts);
            if ($json === false) {
                throw NativeFunctionErroed::createFromLastPhpError();
            }

            $this->formValue('extraScriptTags', $json);
        }

        $this->endpoint = '/forms/chromium/convert/url';

        return $this->request();
    }

    /**
     * Converts an HTML document to PDF.
     *
     * Note: it automatically sets the index filename to "index.html", as
     * required by Gotenberg.
     *
     * See https://gotenberg.dev/docs/modules/chromium#html.
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
     *
     * See https://gotenberg.dev/docs/modules/chromium#markdown.
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
