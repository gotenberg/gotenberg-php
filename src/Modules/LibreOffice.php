<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\Index;
use Gotenberg\MultipartFormDataModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

class LibreOffice
{
    use MultipartFormDataModule;

    private bool $merge = false;

    /**
     * Sets the paper orientation to landscape.
     */
    public function landscape(): self
    {
        $this->formValue('landscape', true);

        return $this;
    }

    /**
     * Set the page ranges to print, e.g., "1-4"'.
     * Empty means all pages.
     *
     * Note: the page ranges are applied to all files independently.
     */
    public function nativePageRanges(string $ranges): self
    {
        $this->formValue('nativePageRanges', $ranges);

        return $this;
    }

    /**
     * Tells Gotenberg to use unoconv for converting the resulting PDF(s) to
     * the "PDF/A-1a" format.
     *
     * @deprecated
     */
    public function nativePdfA1aFormat(): self
    {
        $this->formValue('nativePdfA1aFormat', true);

        return $this;
    }

    /**
     * Tells Gotenberg to use unoconv for converting the resulting PDF to a PDF
     * format.
     */
    public function nativePdfFormat(string $format): self
    {
        $this->formValue('nativePdfFormat', $format);

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
     * Merges the resulting PDFs.
     */
    public function merge(): self
    {
        $this->merge = true;
        $this->formValue('merge', true);

        return $this;
    }

    /**
     * Converts the given document(s) to PDF(s). Gotenberg will return either
     * a unique PDF if you request a merge or a ZIP archive with the PDFs.
     *
     * Note: if you requested a merge, the merging order is determined by the
     * order of the arguments.
     *
     * See https://gotenberg.dev/docs/modules/libreoffice#route.
     */
    public function convert(Stream $file, Stream ...$files): RequestInterface
    {
        $filename = $this->merge ? Index::toAlpha(1) . '_' . $file->getFilename() : $file->getFilename();
        $this->formFile($filename, $file->getStream());

        $index = 2;
        foreach ($files as $file) {
            $filename = $this->merge ? Index::toAlpha($index) . '_' . $file->getFilename() : $file->getFilename();
            $this->formFile($filename, $file->getStream());
            $index++;
        }

        $this->endpoint = '/forms/libreoffice/convert';

        return $this->request();
    }
}
