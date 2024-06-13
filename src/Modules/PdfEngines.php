<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\HrtimeIndex;
use Gotenberg\Index;
use Gotenberg\MultipartFormDataModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

use function json_encode;

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
     * Sets the metadata to write.
     *
     * @param array<string,string|bool|float|int|array<string>> $metadata
     *
     * @throws NativeFunctionErrored
     */
    public function metadata(array $metadata): self
    {
        $json = json_encode($metadata);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        $this->formValue('metadata', $json);

        return $this;
    }

    /**
     * Merges PDFs into a unique PDF.
     *
     * Note: the merging order is determined by the order of the arguments.
     */
    public function merge(Stream ...$pdfs): RequestInterface
    {
        $index = $this->index ?? new HrtimeIndex();

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

    /**
     * Retrieves the metadata of specified PDFs, returning a JSON formatted
     * response with the structure filename => metadata.
     */
    public function readMetadata(Stream $pdf, Stream ...$pdfs): RequestInterface
    {
        $this->formFile($pdf->getFilename(), $pdf->getStream());

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/metadata/read';

        return $this->request();
    }

    /**
     * Allows writing specified metadata to one or more PDF.
     *
     * @param array<string,string|bool|float|int|array<string>> $metadata
     *
     * @throws NativeFunctionErrored
     */
    public function writeMetadata(array $metadata, Stream $pdf, Stream ...$pdfs): RequestInterface
    {
        $this->metadata($metadata);
        $this->formFile($pdf->getFilename(), $pdf->getStream());

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/metadata/write';

        return $this->request();
    }
}
