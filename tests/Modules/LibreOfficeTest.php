<?php

declare(strict_types=1);

namespace Gotenberg\Test\Modules;

use Closure;
use Gotenberg\Gotenberg;
use Gotenberg\InitialView;
use Gotenberg\Magnification;
use Gotenberg\PageLayout;
use Gotenberg\PaperSize;
use Gotenberg\Profile;
use Gotenberg\Stage;
use Gotenberg\Stream;
use Gotenberg\Test\Helpers\Dummies\DummyIndex;
use Gotenberg\Test\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;

final class LibreOfficeTest extends TestCase
{
    /**
     * @param Closure(): RequestInterface                                   $build
     * @param list<array{0: string, 1: string}>                             $values
     * @param list<array{0: string, 1: string, 2: string, 3?: string|null}> $files
     */
    #[Test]
    #[DataProvider('provideRequestData')]
    public function it_creates_a_valid_request(Closure $build, array $values, array $files = []): void
    {
        $this->assertRequest($build(), '/forms/libreoffice/convert', $values, $files);
    }

    /** @return iterable<string, array{0: Closure(): RequestInterface, 1: list<array{0: string, 1: string}>, 2?: list<array{0: string, 1: string, 2: string, 3?: string|null}>}> */
    public static function provideRequestData(): iterable
    {
        yield 'every option' => [
            (static fn (): RequestInterface => Gotenberg::libreOffice('https://my.url')
                ->password('my_password')
                ->paperSize(PaperSize::Legal)
                ->paperDimensions('21cm', 29.7)
                ->landscape()
                ->nativePages('my_nativePages')
                ->singlePage()
                ->skipEmptyPages()
                ->updateIndexes(false)
                ->exportFormFields(false)
                ->allowDuplicateFieldNames()
                ->nativeBookmarks(false)
                ->exportBookmarksToPdfDestination()
                ->exportPlaceholders()
                ->exportNotes()
                ->exportNotesInMargin()
                ->exportNotesPages()
                ->exportOnlyNotesPages()
                ->exportHiddenSlides()
                ->useTransitionEffects(false)
                ->convertOooTargetToPdfTarget()
                ->exportLinksRelativeFsys()
                ->addOriginalDocumentAsStream()
                ->nativeQuality(2)
                ->nativeLosslessImageCompression()
                ->nativeReduceImageResolution()
                ->nativeMaxImageResolution(2)
                ->nativeWatermarkText('my_nativeWatermarkText')
                ->nativeWatermarkColor('#7ffe7f')
                ->nativeWatermarkFontSize(2)
                ->nativeWatermarkRotation(45.5)
                ->nativeWatermarkFont('my_nativeWatermarkFont')
                ->nativeTiledWatermarkText('my_nativeTiledWatermarkText')
                ->nativePdfa(Profile::PdfA2b)
                ->nativePdfua()
                ->nativeTaggedPdf()
                ->initialView(InitialView::Thumbnails)
                ->initialPage(2)
                ->magnification(Magnification::FitWindow)
                ->zoom(2)
                ->pageLayout(PageLayout::TwoColumnRight)
                ->openBookmarkLevels(2)
                ->resizeWindowToInitialPage()
                ->centerWindow()
                ->openInFullScreenMode()
                ->displayPDFDocumentTitle(false)
                ->hideViewerMenubar()
                ->hideViewerToolbar()
                ->hideViewerWindowControls()
                ->convert(Stream::string('my.docx', 'Word content'), Stream::string('my.xlsx', 'Excel content'))),
            [
                ['password', 'my_password'],
                ['paperSize', 'Legal'],
                ['paperWidth', '21cm'],
                ['paperHeight', '29.7'],
                ['landscape', 'true'],
                ['nativePages', 'my_nativePages'],
                ['singlePage', 'true'],
                ['skipEmptyPages', 'true'],
                ['updateIndexes', 'false'],
                ['exportFormFields', 'false'],
                ['allowDuplicateFieldNames', 'true'],
                ['nativeBookmarks', 'false'],
                ['exportBookmarksToPdfDestination', 'true'],
                ['exportPlaceholders', 'true'],
                ['exportNotes', 'true'],
                ['exportNotesInMargin', 'true'],
                ['exportNotesPages', 'true'],
                ['exportOnlyNotesPages', 'true'],
                ['exportHiddenSlides', 'true'],
                ['useTransitionEffects', 'false'],
                ['convertOooTargetToPdfTarget', 'true'],
                ['exportLinksRelativeFsys', 'true'],
                ['addOriginalDocumentAsStream', 'true'],
                ['nativeQuality', '2'],
                ['nativeLosslessImageCompression', 'true'],
                ['nativeReduceImageResolution', 'true'],
                ['nativeMaxImageResolution', '2'],
                ['nativeWatermarkText', 'my_nativeWatermarkText'],
                ['nativeWatermarkColor', '#7ffe7f'],
                ['nativeWatermarkFontSize', '2'],
                ['nativeWatermarkRotation', '45.5'],
                ['nativeWatermarkFont', 'my_nativeWatermarkFont'],
                ['nativeTiledWatermarkText', 'my_nativeTiledWatermarkText'],
                ['nativePdfa', 'PDF/A-2b'],
                ['nativePdfua', 'PDF/UA-1'],
                ['nativeTaggedPdf', 'true'],
                ['initialView', 'thumbnails'],
                ['initialPage', '2'],
                ['magnification', 'fitWindow'],
                ['zoom', '2'],
                ['pageLayout', 'twoColumnRight'],
                ['openBookmarkLevels', '2'],
                ['resizeWindowToInitialPage', 'true'],
                ['centerWindow', 'true'],
                ['openInFullScreenMode', 'true'],
                ['displayPDFDocumentTitle', 'false'],
                ['hideViewerMenubar', 'true'],
                ['hideViewerToolbar', 'true'],
                ['hideViewerWindowControls', 'true'],
            ],
            [
                ['files', 'my.docx', 'Word content'],
                ['files', 'my.xlsx', 'Excel content'],
            ],
        ];

        yield 'merge stage keeps the order of the arguments' => [
            (static fn (): RequestInterface => Gotenberg::libreOffice('https://my.url')
                ->index(new DummyIndex())
                ->stages(Stage::merge(), Stage::remediate(Profile::PdfA2b))
                ->convert(Stream::string('b.docx', 'Word content'), Stream::string('a.xlsx', 'Excel content'))),
            [['stages', '[{"op":"merge"},{"op":"remediate","profile":"PDF\\/A-2b"}]']],
            [
                ['files', '0_b.docx', 'Word content'],
                ['files', '1_a.xlsx', 'Excel content'],
            ],
        ];
    }
}
