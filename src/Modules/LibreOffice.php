<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\MultipartFormDataModule;
use Gotenberg\StagesModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

class LibreOffice
{
    use MultipartFormDataModule;
    use StagesModule;

    /**
     * Sets the password that opens the documents.
     */
    public function password(string $password): static
    {
        $this->formValue('password', $password);

        return $this;
    }

    /**
     * Sets the paper size of every page: "A3", "A4", "A5", "B4", "B5",
     * "Letter", "Legal" or "Tabloid". Without it, each document keeps its own
     * page size.
     */
    public function paperSize(string $size): static
    {
        $this->formValue('paperSize', $size);

        return $this;
    }

    /**
     * Overrides the paper width and height of every page, using various units
     * such as 72pt, 96px, 1in, 25.4mm, 2.54cm, or 6pc.
     */
    public function paperDimensions(float|string $width, float|string $height): static
    {
        $this->formValue('paperWidth', $width);
        $this->formValue('paperHeight', $height);

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
     * Sets the pages to export, in LibreOffice's syntax, e.g., "1-4". The
     * select stage picks pages after the conversion.
     *
     * Note: the pages apply to all files independently.
     */
    public function nativePages(string $pages): static
    {
        $this->formValue('nativePages', $pages);

        return $this;
    }

    /**
     * Puts each sheet of a spreadsheet on one page, hidden sheets included,
     * regardless of its paper size and print ranges.
     */
    public function singlePage(): static
    {
        $this->formValue('singlePage', true);

        return $this;
    }

    /**
     * Suppresses automatically inserted empty pages. Writer documents only.
     */
    public function skipEmptyPages(): static
    {
        $this->formValue('skipEmptyPages', true);

        return $this;
    }

    /**
     * Specifies whether to update the indexes before conversion, keeping in
     * mind that doing so might result in missing links in the final PDF.
     */
    public function updateIndexes(bool $update = true): static
    {
        $this->formValue('updateIndexes', $update);

        return $this;
    }

    /**
     * Specifies whether form fields are exported as widgets or only their fixed
     * print representation is exported.
     */
    public function exportFormFields(bool $export = true): static
    {
        $this->formValue('exportFormFields', $export);

        return $this;
    }

    /**
     * Allows multiple exported form fields to have the same field name.
     */
    public function allowDuplicateFieldNames(): static
    {
        $this->formValue('allowDuplicateFieldNames', true);

        return $this;
    }

    /**
     * Specifies whether bookmarks are exported to PDF.
     */
    public function nativeBookmarks(bool $export = true): static
    {
        $this->formValue('nativeBookmarks', $export);

        return $this;
    }

    /**
     * Exports the bookmarks of the document as Named Destinations.
     */
    public function exportBookmarksToPdfDestination(): static
    {
        $this->formValue('exportBookmarksToPdfDestination', true);

        return $this;
    }

    /**
     * Exports the placeholders fields visual markings only. The exported
     * placeholder is ineffective.
     */
    public function exportPlaceholders(): static
    {
        $this->formValue('exportPlaceholders', true);

        return $this;
    }

    /**
     * Exports the notes.
     */
    public function exportNotes(): static
    {
        $this->formValue('exportNotes', true);

        return $this;
    }

    /**
     * Exports the notes in the margin.
     */
    public function exportNotesInMargin(): static
    {
        $this->formValue('exportNotesInMargin', true);

        return $this;
    }

    /**
     * Exports the notes pages. Impress documents only.
     */
    public function exportNotesPages(): static
    {
        $this->formValue('exportNotesPages', true);

        return $this;
    }

    /**
     * Exports only the notes pages, with exportNotesPages.
     */
    public function exportOnlyNotesPages(): static
    {
        $this->formValue('exportOnlyNotesPages', true);

        return $this;
    }

    /**
     * Exports, for Impress, the slides that are not included in slide shows.
     */
    public function exportHiddenSlides(): static
    {
        $this->formValue('exportHiddenSlides', true);

        return $this;
    }

    /**
     * Specifies whether to use transition effects when displaying the document.
     */
    public function useTransitionEffects(bool $use = true): static
    {
        $this->formValue('useTransitionEffects', $use);

        return $this;
    }

    /**
     * Changes the .od[tpgs] extension of link targets to .pdf. The source
     * document remains untouched.
     */
    public function convertOooTargetToPdfTarget(): static
    {
        $this->formValue('convertOooTargetToPdfTarget', true);

        return $this;
    }

    /**
     * Exports the file system hyperlinks (file:// protocol) as relative to the
     * source document location.
     */
    public function exportLinksRelativeFsys(): static
    {
        $this->formValue('exportLinksRelativeFsys', true);

        return $this;
    }

    /**
     * Inserts a stream that contains the original document, for archiving
     * purposes.
     */
    public function addOriginalDocumentAsStream(): static
    {
        $this->formValue('addOriginalDocumentAsStream', true);

        return $this;
    }

    /**
     * Sets the quality of the JPEG export, from 1 to 100. A higher value
     * produces a higher-quality image and a larger file.
     */
    public function nativeQuality(int $quality): static
    {
        $this->formValue('nativeQuality', $quality);

        return $this;
    }

    /**
     * Exports images with a lossless compression format like PNG, instead of
     * JPEG.
     */
    public function nativeLosslessImageCompression(): static
    {
        $this->formValue('nativeLosslessImageCompression', true);

        return $this;
    }

    /**
     * Reduces the resolution of each image to nativeMaxImageResolution.
     */
    public function nativeReduceImageResolution(): static
    {
        $this->formValue('nativeReduceImageResolution', true);

        return $this;
    }

    /**
     * Sets the resolution, in DPI, nativeReduceImageResolution reduces the
     * images to: 75, 150, 300, 600 or 1200.
     */
    public function nativeMaxImageResolution(int $dpi): static
    {
        $this->formValue('nativeMaxImageResolution', $dpi);

        return $this;
    }

    /**
     * Sets the watermark text LibreOffice draws on each page.
     */
    public function nativeWatermarkText(string $text): static
    {
        $this->formValue('nativeWatermarkText', $text);

        return $this;
    }

    /**
     * Sets the watermark text color, as "#rrggbb".
     */
    public function nativeWatermarkColor(string $color): static
    {
        $this->formValue('nativeWatermarkColor', $color);

        return $this;
    }

    /**
     * Sets the watermark font size.
     */
    public function nativeWatermarkFontSize(int $size): static
    {
        $this->formValue('nativeWatermarkFontSize', $size);

        return $this;
    }

    /**
     * Sets the watermark angle, in degrees, counter-clockwise.
     */
    public function nativeWatermarkRotation(float $degrees): static
    {
        $this->formValue('nativeWatermarkRotation', $degrees);

        return $this;
    }

    /**
     * Sets the watermark font name.
     */
    public function nativeWatermarkFont(string $font): static
    {
        $this->formValue('nativeWatermarkFont', $font);

        return $this;
    }

    /**
     * Sets the tiled watermark text LibreOffice draws on each page.
     */
    public function nativeTiledWatermarkText(string $text): static
    {
        $this->formValue('nativeTiledWatermarkText', $text);

        return $this;
    }

    /**
     * Exports PDF/A: "PDF/A-1b", "PDF/A-2b", "PDF/A-3b" or "PDF/A-4". The
     * remediate stage reaches the other PDF/A levels.
     */
    public function nativePdfa(string $profile): static
    {
        $this->formValue('nativePdfa', $profile);

        return $this;
    }

    /**
     * Exports PDF/UA: "PDF/UA-1".
     */
    public function nativePdfua(string $profile): static
    {
        $this->formValue('nativePdfua', $profile);

        return $this;
    }

    /**
     * Generates a tagged (accessible) PDF without declaring PDF/UA conformance.
     */
    public function nativeTaggedPdf(): static
    {
        $this->formValue('nativeTaggedPdf', true);

        return $this;
    }

    /**
     * Specifies how the document shall be displayed when opened: "default",
     * "outline" or "thumbnails".
     */
    public function initialView(string $view): static
    {
        $this->formValue('initialView', $view);

        return $this;
    }

    /**
     * Specifies the page on which the document opens in the viewer.
     */
    public function initialPage(int $page): static
    {
        $this->formValue('initialPage', $page);

        return $this;
    }

    /**
     * Specifies the magnification when the document opens: "default",
     * "fitVisible", "fitWidth", "fitPage" or "fitActualSize".
     */
    public function magnification(string $magnification): static
    {
        $this->formValue('magnification', $magnification);

        return $this;
    }

    /**
     * Specifies the zoom when the document opens, with the magnification
     * "fitActualSize".
     */
    public function zoom(int $zoom): static
    {
        $this->formValue('zoom', $zoom);

        return $this;
    }

    /**
     * Specifies the page layout when the document opens: "default",
     * "singlePage", "continuous" or "continuousFacing".
     */
    public function pageLayout(string $layout): static
    {
        $this->formValue('pageLayout', $layout);

        return $this;
    }

    /**
     * Specifies how many bookmark levels the viewer opens; -1 opens them all.
     */
    public function openBookmarkLevels(int $levels): static
    {
        $this->formValue('openBookmarkLevels', $levels);

        return $this;
    }

    /**
     * Resizes the viewer window to fit the first page.
     */
    public function resizeWindowToInitialPage(): static
    {
        $this->formValue('resizeWindowToInitialPage', true);

        return $this;
    }

    /**
     * Centers the viewer window on the screen.
     */
    public function centerWindow(): static
    {
        $this->formValue('centerWindow', true);

        return $this;
    }

    /**
     * Opens the document in full-screen mode.
     */
    public function openInFullScreenMode(): static
    {
        $this->formValue('openInFullScreenMode', true);

        return $this;
    }

    /**
     * Specifies whether the viewer's title bar shows the document title instead
     * of the file name.
     */
    public function displayPDFDocumentTitle(bool $display = true): static
    {
        $this->formValue('displayPDFDocumentTitle', $display);

        return $this;
    }

    /**
     * Hides the viewer's menu bar.
     */
    public function hideViewerMenubar(): static
    {
        $this->formValue('hideViewerMenubar', true);

        return $this;
    }

    /**
     * Hides the viewer's toolbar.
     */
    public function hideViewerToolbar(): static
    {
        $this->formValue('hideViewerToolbar', true);

        return $this;
    }

    /**
     * Hides the viewer's window controls.
     */
    public function hideViewerWindowControls(): static
    {
        $this->formValue('hideViewerWindowControls', true);

        return $this;
    }

    /**
     * Converts the given documents to PDFs. Gotenberg returns one PDF per
     * document, in a ZIP archive when there are several, unless a merge stage
     * joins them in the order of the arguments.
     */
    public function convert(Stream ...$files): RequestInterface
    {
        $this->inputFiles(...$files);
        $this->endpoint = '/forms/libreoffice/convert';

        return $this->request();
    }
}
