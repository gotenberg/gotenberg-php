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
     * Sets the page ranges to print, e.g., "1-4"'.
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
     * Specifies whether form fields are exported as widgets or only their fixed
     * print representation is exported.
     */
    public function exportFormFields(bool $export = true): self
    {
        $this->formValue('exportFormFields', $export ?: '0');

        return $this;
    }

    /**
     * Specifies whether multiple form fields exported are allowed to have the
     * same field name.
     */
    public function allowDuplicateFieldNames(): self
    {
        $this->formValue('allowDuplicateFieldNames', true);

        return $this;
    }

    /**
     * Specifies if bookmarks are exported to PDF.
     */
    public function exportBookmarks(bool $export = true): self
    {
        $this->formValue('exportBookmarks', $export ?: '0');

        return $this;
    }

    /**
     * Specifies that the bookmarks contained in the source LibreOffice file
     * should be exported to the PDF file as Named Destination.
     */
    public function exportBookmarksToPdfDestination(): self
    {
        $this->formValue('exportBookmarksToPdfDestination', true);

        return $this;
    }

    /**
     * Exports the placeholders fields visual markings only. The exported
     * placeholder is ineffective.
     */
    public function exportPlaceholders(): self
    {
        $this->formValue('exportPlaceholders', true);

        return $this;
    }

    /**
     * Specifies if notes are exported to PDF.
     */
    public function exportNotes(): self
    {
        $this->formValue('exportNotes', true);

        return $this;
    }

    /**
     * Specifies if notes pages are exported to PDF. Notes pages are available
     * in Impress documents only.
     */
    public function exportNotesPages(): self
    {
        $this->formValue('exportNotesPages', true);

        return $this;
    }

    /**
     * Specifies, if the form field exportNotesPages is set to true, if only
     * notes pages are exported to PDF.
     */
    public function exportOnlyNotesPages(): self
    {
        $this->formValue('exportOnlyNotesPages', true);

        return $this;
    }

    /**
     * Specifies if notes in margin are exported to PDF.
     */
    public function exportNotesInMargin(): self
    {
        $this->formValue('exportNotesInMargin', true);

        return $this;
    }

    /**
     * Specifies that the target documents with .od[tpgs] extension, will have
     * that extension changed to .pdf when the link is exported to PDF. The
     * source document remains untouched.
     */
    public function convertOooTargetToPdfTarget(): self
    {
        $this->formValue('convertOooTargetToPdfTarget', true);

        return $this;
    }

    /**
     * Specifies that the file system related hyperlinks (file:// protocol)
     * present in the document will be exported as relative to the source
     * document location.
     */
    public function exportLinksRelativeFsys(): self
    {
        $this->formValue('exportLinksRelativeFsys', true);

        return $this;
    }

    /**
     * Exports, for LibreOffice Impress, slides that are not included in slide
     * shows.
     */
    public function exportHiddenSlides(): self
    {
        $this->formValue('exportHiddenSlides', true);

        return $this;
    }

    /**
     * Specifies that automatically inserted empty pages are suppressed. This
     * option is active only if storing Writer documents.
     */
    public function skipEmptyPages(): self
    {
        $this->formValue('skipEmptyPages', true);

        return $this;
    }

    /**
     * Specifies that a stream is inserted to the PDF file which contains the
     * original document for archiving purposes.
     */
    public function addOriginalDocumentAsStream(): self
    {
        $this->formValue('addOriginalDocumentAsStream', true);

        return $this;
    }

    /**
     * Ignores each sheet’s paper size, print ranges and shown/hidden status
     * and puts every sheet (even hidden sheets) on exactly one page.
     */
    public function singlePageSheets(): self
    {
        $this->formValue('singlePageSheets', true);

        return $this;
    }

    /**
     * Specifies if images are exported to PDF using a lossless compression
     * format like PNG or compressed using the JPEG format.
     */
    public function losslessImageCompression(): self
    {
        $this->formValue('losslessImageCompression', true);

        return $this;
    }

    /**
     * Specifies the quality of the JPG export. A higher value produces a
     * higher-quality image and a larger file. Between 1 and 100.
     */
    public function quality(int $quality): self
    {
        $this->formValue('quality', $quality);

        return $this;
    }

    /**
     * Specifies if the resolution of each image is reduced to the resolution
     * specified by the form field maxImageResolution.
     * FIXME: parameter not used.
     */
    public function reduceImageResolution(bool $notUsedAnymore = true): self
    {
        $this->formValue('reduceImageResolution', true);

        return $this;
    }

    /**
     * If the form field reduceImageResolution is set to true, tells if all
     * images will be reduced to the given value in DPI. Possible values are:
     * 75, 150, 300, 600 and 1200.
     */
    public function maxImageResolution(int $dpi): self
    {
        $this->formValue('maxImageResolution', $dpi);

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
