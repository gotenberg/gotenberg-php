<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\EmbedMetadata;
use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\HrtimeIndex;
use Gotenberg\Index;
use Gotenberg\MultipartFormDataModule;
use Gotenberg\SplitMode;
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
     * Defines whether the resulting PDF should be flattened.
     * Prefer the flatten method if you only want to flatten one or more PDFs.
     */
    public function flattening(): self
    {
        $this->formValue('flatten', true);

        return $this;
    }

    /**
     * Defines whether the resulting PDF should be encrypted.
     * Prefer the encrypt method if you only want to encrypt one or more PDFs.
     */
    public function encrypting(string $userPassword, string $ownerPassword = ''): self
    {
        $this->formValue('userPassword', $userPassword);
        $this->formValue('ownerPassword', $ownerPassword);

        return $this;
    }

    /**
     * Sets the file to embed in the resulting PDF.
     */
    public function embeds(Stream ...$embeds): self
    {
        foreach ($embeds as $embed) {
            $this->formFile($embed->getFilename(), $embed->getStream(), 'embeds');
        }

        return $this;
    }

    /**
     * Sets metadata on embedded files in the resulting PDF, required for
     * Factur-X / ZUGFeRD compliance.
     *
     * @throws NativeFunctionErrored
     */
    public function embedsMetadata(EmbedMetadata ...$metadata): self
    {
        $map = [];
        foreach ($metadata as $entry) {
            $map[$entry->filename] = $entry;
        }

        $json = json_encode($map);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        $this->formValue('embedsMetadata', $json);

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
     * Splits PDF(s).
     * Gotenberg will return the PDF or a ZIP archive with the PDFs.
     */
    public function split(SplitMode $mode, Stream ...$pdfs): RequestInterface
    {
        $this->formValue('splitMode', $mode->mode);
        $this->formValue('splitSpan', $mode->span);
        $this->formValue('splitUnify', $mode->unify ?: '0');

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/split';

        return $this->request();
    }

    /**
     * Flatten PDF(s).
     * Gotenberg will return the PDF or a ZIP archive with the PDFs.
     */
    public function flatten(Stream ...$pdfs): RequestInterface
    {
        $this->formValue('flatten', true);

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/flatten';

        return $this->request();
    }

    /**
     * Converts PDF(s) to a specific PDF/A format.
     * Gotenberg will return the PDF or a ZIP archive with the PDFs.
     */
    public function convert(string $pdfa, Stream ...$pdfs): RequestInterface
    {
        $this->pdfa($pdfa);

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
    public function readMetadata(Stream ...$pdfs): RequestInterface
    {
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
    public function writeMetadata(array $metadata, Stream ...$pdfs): RequestInterface
    {
        $this->metadata($metadata);

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/metadata/write';

        return $this->request();
    }

    /**
     * Allows encrypting one or more PDF.
     *
     * @throws NativeFunctionErrored
     */
    public function encrypt(string $userPassword, string $ownerPassword = '', Stream ...$pdfs): RequestInterface
    {
        $this->encrypting($userPassword, $ownerPassword);

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/encrypt';

        return $this->request();
    }

    /**
     * Sets the bookmarks for the merge route.
     */
    public function bookmarks(string $bookmarks): self
    {
        $this->formValue('bookmarks', $bookmarks);

        return $this;
    }

    /**
     * Enables auto-indexing of bookmarks for the merge route.
     */
    public function autoIndexBookmarks(): self
    {
        $this->formValue('autoIndexBookmarks', true);

        return $this;
    }

    /**
     * Watermarks PDF(s).
     * Gotenberg will return the PDF or a ZIP archive with the PDFs.
     */
    public function watermark(string $source, Stream ...$pdfs): RequestInterface
    {
        $this->formValue('watermarkSource', $source);

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/watermark';

        return $this->request();
    }

    /**
     * Stamps PDF(s).
     * Gotenberg will return the PDF or a ZIP archive with the PDFs.
     */
    public function stamp(string $source, Stream ...$pdfs): RequestInterface
    {
        $this->formValue('stampSource', $source);

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/stamp';

        return $this->request();
    }

    /**
     * Rotates PDF(s).
     * Gotenberg will return the PDF or a ZIP archive with the PDFs.
     */
    public function rotate(int $angle, Stream ...$pdfs): RequestInterface
    {
        $this->formValue('rotateAngle', $angle);

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/rotate';

        return $this->request();
    }

    /**
     * Retrieves the bookmarks of specified PDFs, returning a JSON formatted
     * response with the structure filename => bookmarks.
     */
    public function readBookmarks(Stream ...$pdfs): RequestInterface
    {
        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/bookmarks/read';

        return $this->request();
    }

    /**
     * Allows writing specified bookmarks to one or more PDF.
     */
    public function writeBookmarks(string $bookmarks, Stream ...$pdfs): RequestInterface
    {
        $this->formValue('bookmarks', $bookmarks);

        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->endpoint = '/forms/pdfengines/bookmarks/write';

        return $this->request();
    }

    /**
     * Allows embedding one or more files to one or more PDF.
     *
     * @param Stream[] $embeds
     *
     * @throws NativeFunctionErrored
     */
    public function embed(array $embeds, Stream ...$pdfs): RequestInterface
    {
        foreach ($pdfs as $pdf) {
            $this->formFile($pdf->getFilename(), $pdf->getStream());
        }

        $this->embeds(...$embeds);

        $this->endpoint = '/forms/pdfengines/embed';

        return $this->request();
    }
}
