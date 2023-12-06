<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\HrtimeIndex;
use Gotenberg\Index;
use Gotenberg\MultipartFormDataModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

class LibreOffice
{
    use MultipartFormDataModule;

    private Index|null $index = null;
    private bool $merge       = false;

    /**
     * Overrides the default index generator for ordering
     * files we want to merge.
     */
    public function index(Index $index): self
    {
        $this->index = $index;

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
     */
    public function convert(Stream $file, Stream ...$files): RequestInterface
    {
        $index    = $this->index ?? new HrtimeIndex();
        $filename = $this->merge ? $index->create() . '_' . $file->getFilename() : $file->getFilename();
        $this->formFile($filename, $file->getStream());

        foreach ($files as $file) {
            $filename = $this->merge ? $index->create() . '_' . $file->getFilename() : $file->getFilename();
            $this->formFile($filename, $file->getStream());
        }

        $this->endpoint = '/forms/libreoffice/convert';

        return $this->request();
    }
}
