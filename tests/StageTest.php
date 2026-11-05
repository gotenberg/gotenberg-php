<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\ColorSpace;
use Gotenberg\EncryptionAlgorithm;
use Gotenberg\FacturXDocumentType;
use Gotenberg\FacturXLevel;
use Gotenberg\Font;
use Gotenberg\Pages;
use Gotenberg\Permission;
use Gotenberg\Profile;
use Gotenberg\Relationship;
use Gotenberg\RenderFormat;
use Gotenberg\Stage;
use Gotenberg\Stream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use function array_map;
use function json_encode;

use const JSON_THROW_ON_ERROR;

final class StageTest extends TestCase
{
    /**
     * @param array<string,string> $formFields
     * @param list<string>         $files
     */
    #[Test]
    #[DataProvider('provideStageData')]
    public function it_serializes_a_stage(Stage $stage, string $json, array $formFields, array $files = []): void
    {
        $this->assertSame($json, json_encode($stage, JSON_THROW_ON_ERROR));
        $this->assertSame($formFields, $stage->formFields());
        $this->assertSame($files, array_map(static fn (Stream $file): string => $file->filename, $stage->files()));
    }

    /** @return iterable<string, array{0: Stage, 1: string, 2: array<string,string>, 3?: list<string>}> */
    public static function provideStageData(): iterable
    {
        $pdf   = Stream::string('letterhead.pdf', 'PDF');
        $image = Stream::string('logo.png', 'PNG');
        $xml   = Stream::string('invoice.xml', 'XML');
        $cert  = Stream::string('cert.pem', 'CERT');
        $key   = Stream::string('key.pem', 'KEY');
        $file  = Stream::string('data.csv', 'CSV');

        yield 'merge' => [Stage::merge(), '{"op":"merge"}', []];
        yield 'split every' => [Stage::splitEvery(2), '{"op":"split","every":2}', ['every' => '2']];
        yield 'split ranges' => [Stage::splitRanges('1', '2-3', Pages::Odd), '{"op":"split","ranges":["1","2-3","odd"]}', ['ranges' => '["1","2-3","odd"]']];
        yield 'select' => [Stage::select('1-2'), '{"op":"select","pages":"1-2"}', ['pages' => '1-2']];
        yield 'delete' => [Stage::delete(Pages::Last), '{"op":"delete","pages":"last"}', ['pages' => 'last']];
        yield 'insert' => [
            Stage::insert($pdf, 1, '2'),
            '{"op":"insert","source":"letterhead.pdf","at":1,"sourcePages":"2"}',
            ['source' => 'letterhead.pdf', 'at' => '1', 'sourcePages' => '2'],
            ['letterhead.pdf'],
        ];

        yield 'rotate' => [Stage::rotate(90), '{"op":"rotate","angle":90}', ['angle' => '90']];
        yield 'stamp text' => [
            Stage::stampText('DRAFT', Pages::All, 72, 72.5, 24, Font::HelveticaBold, '#ff0000', 0.5, 45),
            '{"op":"stamp-text","text":"DRAFT","pages":"all","x":72,"y":72.5,"fontSize":24,"font":"Helvetica-Bold","color":"#ff0000","opacity":0.5,"rotation":45}',
            ['text' => 'DRAFT', 'pages' => 'all', 'x' => '72', 'y' => '72.5', 'fontSize' => '24', 'font' => 'Helvetica-Bold', 'color' => '#ff0000', 'opacity' => '0.5', 'rotation' => '45'],
        ];

        yield 'stamp image' => [
            Stage::stampImage($image, width: 100),
            '{"op":"stamp-image","image":"logo.png","width":100}',
            ['image' => 'logo.png', 'width' => '100'],
            ['logo.png'],
        ];

        yield 'stamp pdf' => [
            Stage::stampPdf($pdf, 1, '1', 0, 0, 1, 1, 0),
            '{"op":"stamp-pdf","source":"letterhead.pdf","sourcePage":1,"pages":"1","x":0,"y":0,"scale":1,"opacity":1,"rotation":0}',
            ['source' => 'letterhead.pdf', 'sourcePage' => '1', 'pages' => '1', 'x' => '0', 'y' => '0', 'scale' => '1', 'opacity' => '1', 'rotation' => '0'],
            ['letterhead.pdf'],
        ];

        yield 'set metadata' => [
            Stage::setMetadata('Title', 'Author', 'Subject', 'Keywords', 'Creator', 'Producer', 'D:20260101120000Z', 'D:20260102120000Z'),
            '{"op":"set-metadata","title":"Title","author":"Author","subject":"Subject","keywords":"Keywords","creator":"Creator","producer":"Producer","creationDate":"D:20260101120000Z","modDate":"D:20260102120000Z"}',
            ['title' => 'Title', 'author' => 'Author', 'subject' => 'Subject', 'keywords' => 'Keywords', 'creator' => 'Creator', 'producer' => 'Producer', 'creationDate' => 'D:20260101120000Z', 'modDate' => 'D:20260102120000Z'],
        ];

        yield 'set metadata partially' => [Stage::setMetadata(title: 'Title'), '{"op":"set-metadata","title":"Title"}', ['title' => 'Title']];
        yield 'set bookmarks' => [
            Stage::setBookmarks([['title' => 'Summary', 'pageNumber' => 1, 'children' => [['title' => 'Figures', 'pageNumber' => 2]]]]),
            '{"op":"set-bookmarks","bookmarks":[{"title":"Summary","pageNumber":1,"children":[{"title":"Figures","pageNumber":2}]}]}',
            ['bookmarks' => '[{"title":"Summary","pageNumber":1,"children":[{"title":"Figures","pageNumber":2}]}]'],
        ];

        yield 'set form' => [
            Stage::setForm(['agree' => 'Yes', 'colors' => ['Red', 'Green']]),
            '{"op":"set-form","fields":{"agree":"Yes","colors":["Red","Green"]}}',
            ['fields' => '{"agree":"Yes","colors":["Red","Green"]}'],
        ];

        yield 'flatten form' => [Stage::flattenForm(), '{"op":"flatten-form"}', []];
        yield 'flatten transparency' => [Stage::flattenTransparency(), '{"op":"flatten-transparency"}', []];
        yield 'convert colors' => [Stage::convertColors(ColorSpace::Cmyk), '{"op":"convert-colors","target":"cmyk"}', ['target' => 'cmyk']];
        yield 'redact' => [
            Stage::redact([['page' => 1, 'x' => 160, 'y' => 435, 'width' => 60, 'height' => 25]]),
            '{"op":"redact","regions":[{"page":1,"x":160,"y":435,"width":60,"height":25}]}',
            ['regions' => '[{"page":1,"x":160,"y":435,"width":60,"height":25}]'],
        ];

        yield 'autotag' => [Stage::autotag(), '{"op":"autotag"}', []];
        yield 'attach' => [
            Stage::attach($file, 'data.csv', 'text/csv', Relationship::Data),
            '{"op":"attach","file":"data.csv","name":"data.csv","mimeType":"text\/csv","relationship":"Data"}',
            ['file' => 'data.csv', 'name' => 'data.csv', 'mimeType' => 'text/csv', 'relationship' => 'Data'],
            ['data.csv'],
        ];

        yield 'detach' => [Stage::detach('data.csv'), '{"op":"detach","name":"data.csv"}', ['name' => 'data.csv']];
        yield 'optimize' => [Stage::optimize(), '{"op":"optimize"}', []];
        yield 'optimize without font subsetting' => [
            Stage::optimize(subsetFonts: false, dedup: true),
            '{"op":"optimize","dedup":true,"subsetFonts":false}',
            ['dedup' => 'true', 'subsetFonts' => 'false'],
        ];

        yield 'encrypt' => [
            Stage::encrypt('user', 'owner', EncryptionAlgorithm::Aes256, [Permission::Print, Permission::ExtractText]),
            '{"op":"encrypt","userPassword":"user","ownerPassword":"owner","algorithm":"aes256","permissions":{"print":false,"extractText":false}}',
            ['userPassword' => 'user', 'ownerPassword' => 'owner', 'algorithm' => 'aes256', 'permissions' => '{"print":false,"extractText":false}'],
        ];

        yield 'decrypt' => [Stage::decrypt('secret'), '{"op":"decrypt","password":"secret"}', ['password' => 'secret']];
        yield 'decrypt without password' => [Stage::decrypt(), '{"op":"decrypt"}', []];
        yield 'remediate' => [
            Stage::remediate([Profile::PdfA2b, Profile::PdfUa1], 'en-US', true, 'A chart'),
            '{"op":"remediate","profile":["PDF\/A-2b","PDF\/UA-1"],"language":"en-US","aggressive":true,"figureAltText":"A chart"}',
            ['profile' => '["PDF\/A-2b","PDF\/UA-1"]', 'language' => 'en-US', 'aggressive' => 'true', 'figureAltText' => 'A chart'],
        ];

        yield 'remediate factur-x' => [
            Stage::remediate(Profile::FacturX, xml: $xml, level: FacturXLevel::En16931, documentType: FacturXDocumentType::Invoice),
            '{"op":"remediate","profile":"Factur-X","xml":"invoice.xml","level":"EN 16931","documentType":"INVOICE"}',
            ['profile' => 'Factur-X', 'xml' => 'invoice.xml', 'level' => 'EN 16931', 'documentType' => 'INVOICE'],
            ['invoice.xml'],
        ];

        yield 'sign' => [
            Stage::sign($cert, $key, 'pass', 'Approval', 'Paris', 'me@example.com', 'Me', true, 'https://tsa.example.com'),
            '{"op":"sign","certificate":"cert.pem","key":"key.pem","certificatePassword":"pass","reason":"Approval","location":"Paris","contactInfo":"me@example.com","name":"Me","pades":true,"timestampUrl":"https:\/\/tsa.example.com"}',
            ['certificate' => 'cert.pem', 'key' => 'key.pem', 'certificatePassword' => 'pass', 'reason' => 'Approval', 'location' => 'Paris', 'contactInfo' => 'me@example.com', 'name' => 'Me', 'pades' => 'true', 'timestampUrl' => 'https://tsa.example.com'],
            ['cert.pem', 'key.pem'],
        ];

        yield 'timestamp' => [
            Stage::timestamp('https://tsa.example.com'),
            '{"op":"timestamp","timestampUrl":"https:\/\/tsa.example.com"}',
            ['timestampUrl' => 'https://tsa.example.com'],
        ];

        yield 'render' => [
            Stage::render('1', 300, RenderFormat::Jpeg, 80),
            '{"op":"render","pages":"1","dpi":300,"format":"jpeg","quality":80}',
            ['pages' => '1', 'dpi' => '300', 'format' => 'jpeg', 'quality' => '80'],
        ];
    }
}
