<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\Index;
use Gotenberg\MultipartFormDataModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

class PdfEngines
{
    use MultipartFormDataModule;

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
     * Merges PDFs into a unique PDF.
     *
     * Note: the merging order is determined by the order of the arguments.
     *
     * See https://gotenberg.dev/docs/modules/pdf-engines#merge.
     */
    public function merge(Stream $pdf1, Stream $pdf2, Stream ...$pdfs): RequestInterface
    {
        $this->formFile(Index::toAlpha(1) . '_' . $pdf1->getFilename(), $pdf1->getStream());
        $this->formFile(Index::toAlpha(2) . '_' . $pdf2->getFilename(), $pdf2->getStream());

        $index = 3;
        foreach ($pdfs as $pdf) {
            $this->formFile(Index::toAlpha($index) . '_' . $pdf->getFilename(), $pdf->getStream());
            $index++;
        }

        $this->endpoint = '/forms/pdfengines/merge';

        return $this->request();
    }

    /**
     * Converts PDF(s) to a specific PDF format.
     * Gotenberg will return the PDF or a ZIP archive with the PDFs.
     *
     * See:
     * https://gotenberg.dev/docs/modules/pdf-engines#convert.
     * https://gotenberg.dev/docs/modules/pdf-engines#engines.
     */
    public function convert(string $pdfFormat, Stream $pdf, Stream ...$pdfs): RequestInterface
    {
        $this->formValue('pdfFormat', $pdfFormat);
        $this->formFile($pdf->getFilename(), $pdf->getStream());

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/convert';

        return $this->request();
    }
}
