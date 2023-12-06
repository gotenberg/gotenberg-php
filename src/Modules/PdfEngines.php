<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\HrtimeIndex;
use Gotenberg\Index;
use Gotenberg\MultipartFormDataModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

class PdfEngines
{
    use MultipartFormDataModule;

    private Index|null $index = null;

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
     * Merges PDFs into a unique PDF.
     *
     * Note: the merging order is determined by the order of the arguments.
     */
    public function merge(Stream $pdf1, Stream $pdf2, Stream ...$pdfs): RequestInterface
    {
        $index = $this->index ?? new HrtimeIndex();

        $this->formFile($index->create() . '_' . $pdf1->getFilename(), $pdf1->getStream());
        $this->formFile($index->create() . '_' . $pdf2->getFilename(), $pdf2->getStream());

        foreach ($pdfs as $pdf) {
            $this->formFile($index->create() . '_' . $pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/merge';

        return $this->request();
    }

    /**
     * Converts PDF(s) to a specific PDF/A format.
     * Gotenberg will return the PDF or a ZIP archive with the PDFs.
     */
    public function convert(string $pdfa, Stream $pdf, Stream ...$pdfs): RequestInterface
    {
        $this->pdfa($pdfa);
        $this->formFile($pdf->getFilename(), $pdf->getStream());

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/convert';

        return $this->request();
    }
}
