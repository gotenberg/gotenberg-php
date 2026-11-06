<?php

declare(strict_types=1);

namespace Gotenberg;

use BackedEnum;
use JsonSerializable;

use function array_filter;
use function array_map;
use function array_values;
use function is_array;
use function is_bool;
use function json_encode;

use const JSON_THROW_ON_ERROR;

/**
 * A stage is one operation on a PDF: merge, stamp, sign, make PDF/A, and so
 * on. Gotenberg runs a list of stages on the PDFs it converts, or on the PDFs
 * you upload to the pdf module.
 *
 * Options left to null keep Gotenberg's default. Pages take a page ("3"), a
 * range ("2-5"), a list of both ("1,3,5-7"), or a Pages keyword.
 *
 * @phpstan-type Option string|int|float|bool|Stream|BackedEnum|array<mixed>
 */
final readonly class Stage implements JsonSerializable
{
    /** @param array<string,Option> $options */
    private function __construct(public string $op, public array $options = [])
    {
    }

    /** @param array<string,Option|null> $options */
    private static function of(string $op, array $options = []): self
    {
        return new self($op, array_filter($options, static fn (mixed $value): bool => $value !== null));
    }

    /**
     * Appends the PDFs into one, in the order you pass them.
     */
    public static function merge(): self
    {
        return self::of('merge');
    }

    /**
     * Cuts each PDF every $every pages. The stages after it run on each part.
     */
    public static function splitEvery(int $every): self
    {
        return self::of('split', ['every' => $every]);
    }

    /**
     * Cuts each PDF into one part per page selection, such as "1-3", "4,6" or
     * Pages::Odd.
     * The stages after it run on each part.
     */
    public static function splitRanges(string|Pages ...$ranges): self
    {
        return self::of('split', ['ranges' => array_values($ranges)]);
    }

    /**
     * Keeps the pages you list, in the order you list them.
     */
    public static function select(string|Pages $pages): self
    {
        return self::of('select', ['pages' => $pages]);
    }

    /**
     * Removes the pages you list.
     */
    public static function delete(string|Pages $pages): self
    {
        return self::of('delete', ['pages' => $pages]);
    }

    /**
     * Inserts the pages of another PDF after page $at; 0 inserts before the
     * first page.
     */
    public static function insert(Stream $source, int|null $at = null, string|Pages|null $sourcePages = null): self
    {
        return self::of('insert', [
            'source' => $source,
            'at' => $at,
            'sourcePages' => $sourcePages,
        ]);
    }

    /**
     * Sets the rotation of pages, a multiple of 90. Viewers turn them
     * clockwise.
     */
    public static function rotate(int $angle, string|Pages|null $pages = null): self
    {
        return self::of('rotate', ['angle' => $angle, 'pages' => $pages]);
    }

    /**
     * Draws a line of text over pages. Positions are in points from the
     * bottom-left corner, the color is "#rrggbb", and the font one of the 14
     * standard PDF fonts.
     */
    public static function stampText(
        string $text,
        string|Pages|null $pages = null,
        float|null $x = null,
        float|null $y = null,
        float|null $fontSize = null,
        Font|null $font = null,
        string|null $color = null,
        float|null $opacity = null,
        float|null $rotation = null,
    ): self {
        return self::of('stamp-text', [
            'text' => $text,
            'pages' => $pages,
            'x' => $x,
            'y' => $y,
            'fontSize' => $fontSize,
            'font' => $font,
            'color' => $color,
            'opacity' => $opacity,
            'rotation' => $rotation,
        ]);
    }

    /**
     * Draws a PNG or JPEG image over pages. Positions and sizes are in points
     * from the bottom-left corner.
     */
    public static function stampImage(
        Stream $image,
        string|Pages|null $pages = null,
        float|null $x = null,
        float|null $y = null,
        float|null $width = null,
        float|null $height = null,
        float|null $opacity = null,
        float|null $rotation = null,
    ): self {
        return self::of('stamp-image', [
            'image' => $image,
            'pages' => $pages,
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'opacity' => $opacity,
            'rotation' => $rotation,
        ]);
    }

    /**
     * Draws a page of another PDF over pages: a letterhead, a seal.
     */
    public static function stampPdf(
        Stream $source,
        int|null $sourcePage = null,
        string|Pages|null $pages = null,
        float|null $x = null,
        float|null $y = null,
        float|null $scale = null,
        float|null $opacity = null,
        float|null $rotation = null,
    ): self {
        return self::of('stamp-pdf', [
            'source' => $source,
            'sourcePage' => $sourcePage,
            'pages' => $pages,
            'x' => $x,
            'y' => $y,
            'scale' => $scale,
            'opacity' => $opacity,
            'rotation' => $rotation,
        ]);
    }

    /**
     * Writes the document information. A field left to null keeps its value.
     * Dates are PDF dates, such as "D:20260101120000Z".
     */
    public static function setMetadata(
        string|null $title = null,
        string|null $author = null,
        string|null $subject = null,
        string|null $keywords = null,
        string|null $creator = null,
        string|null $producer = null,
        string|null $creationDate = null,
        string|null $modDate = null,
    ): self {
        return self::of('set-metadata', [
            'title' => $title,
            'author' => $author,
            'subject' => $subject,
            'keywords' => $keywords,
            'creator' => $creator,
            'producer' => $producer,
            'creationDate' => $creationDate,
            'modDate' => $modDate,
        ]);
    }

    /**
     * Replaces the bookmarks.
     *
     * @param list<array{title: string, pageNumber: int, children?: list<array<string,mixed>>}> $bookmarks
     */
    public static function setBookmarks(array $bookmarks): self
    {
        return self::of('set-bookmarks', ['bookmarks' => $bookmarks]);
    }

    /**
     * Fills form fields, keyed by their fully qualified names: a string, or a
     * list of strings for a list that selects several.
     *
     * @param array<string,string|list<string>> $fields
     */
    public static function setForm(array $fields): self
    {
        return self::of('set-form', ['fields' => $fields]);
    }

    /**
     * Burns the form fields into the pages.
     */
    public static function flattenForm(): self
    {
        return self::of('flatten-form');
    }

    /**
     * Replaces transparent content with opaque content.
     */
    public static function flattenTransparency(): self
    {
        return self::of('flatten-transparency');
    }

    /**
     * Converts every color to one color space.
     */
    public static function convertColors(ColorSpace $target): self
    {
        return self::of('convert-colors', ['target' => $target]);
    }

    /**
     * Removes the content under each region from the file itself. Regions are
     * in points from the bottom-left corner.
     *
     * @param list<array{page: int, x: float, y: float, width: float, height: float}> $regions
     */
    public static function redact(array $regions): self
    {
        return self::of('redact', ['regions' => $regions]);
    }

    /**
     * Infers a structure tree for an untagged PDF.
     */
    public static function autotag(): self
    {
        return self::of('autotag');
    }

    /**
     * Embeds a file in the PDF. $relationship makes it a PDF/A-3 associated
     * file.
     */
    public static function attach(
        Stream $file,
        string|null $name = null,
        string|null $mimeType = null,
        Relationship|null $relationship = null,
    ): self {
        return self::of('attach', [
            'file' => $file,
            'name' => $name,
            'mimeType' => $mimeType,
            'relationship' => $relationship,
        ]);
    }

    /**
     * Removes an embedded file.
     */
    public static function detach(string $name): self
    {
        return self::of('detach', ['name' => $name]);
    }

    /**
     * Rewrites the PDF to shrink it, without losing quality. Every option is
     * on unless you turn it off.
     */
    public static function optimize(
        bool|null $objectStreams = null,
        bool|null $dedup = null,
        bool|null $recompressImages = null,
        bool|null $subsetFonts = null,
    ): self {
        return self::of('optimize', [
            'objectStreams' => $objectStreams,
            'dedup' => $dedup,
            'recompressImages' => $recompressImages,
            'subsetFonts' => $subsetFonts,
        ]);
    }

    /**
     * Protects the PDF with passwords. A reader without the owner password may
     * do everything but what $deny lists.
     *
     * @param list<Permission>|null $deny
     */
    public static function encrypt(
        string|null $userPassword = null,
        string|null $ownerPassword = null,
        EncryptionAlgorithm|null $algorithm = null,
        array|null $deny = null,
    ): self {
        $permissions = null;
        if ($deny !== null && $deny !== []) {
            $permissions = [];
            foreach ($deny as $permission) {
                $permissions[$permission->value] = false;
            }
        }

        return self::of('encrypt', [
            'userPassword' => $userPassword,
            'ownerPassword' => $ownerPassword,
            'algorithm' => $algorithm,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Opens encrypted PDFs and removes their encryption.
     */
    public static function decrypt(string|null $password = null): self
    {
        return self::of('decrypt', ['password' => $password]);
    }

    /**
     * Repairs the PDF until it conforms to a profile, or to a PDF/A profile,
     * or Factur-X, and a PDF/UA one together.
     *
     * For Factur-X, $xml embeds an invoice XML, with its $level and
     * $documentType.
     *
     * @param Profile|list<Profile> $profile
     */
    public static function remediate(
        Profile|array $profile,
        string|null $language = null,
        bool|null $aggressive = null,
        string|null $figureAltText = null,
        Stream|null $xml = null,
        FacturXLevel|null $level = null,
        FacturXDocumentType|null $documentType = null,
    ): self {
        return self::of('remediate', [
            'profile' => $profile,
            'language' => $language,
            'aggressive' => $aggressive,
            'figureAltText' => $figureAltText,
            'xml' => $xml,
            'level' => $level,
            'documentType' => $documentType,
        ]);
    }

    /**
     * Signs the PDF with a PKCS#12 certificate, or a PEM certificate and its
     * $key.
     */
    public static function sign(
        Stream $certificate,
        Stream|null $key = null,
        string|null $certificatePassword = null,
        string|null $reason = null,
        string|null $location = null,
        string|null $contactInfo = null,
        string|null $name = null,
        bool|null $pades = null,
        string|null $timestampUrl = null,
    ): self {
        return self::of('sign', [
            'certificate' => $certificate,
            'key' => $key,
            'certificatePassword' => $certificatePassword,
            'reason' => $reason,
            'location' => $location,
            'contactInfo' => $contactInfo,
            'name' => $name,
            'pades' => $pades,
            'timestampUrl' => $timestampUrl,
        ]);
    }

    /**
     * Adds a document timestamp from an RFC 3161 timestamp authority.
     */
    public static function timestamp(string $timestampUrl): self
    {
        return self::of('timestamp', ['timestampUrl' => $timestampUrl]);
    }

    /**
     * Draws pages as images.
     */
    public static function render(
        string|Pages|null $pages = null,
        int|null $dpi = null,
        RenderFormat|null $format = null,
        int|null $quality = null,
    ): self {
        return self::of('render', [
            'pages' => $pages,
            'dpi' => $dpi,
            'format' => $format,
            'quality' => $quality,
        ]);
    }

    /**
     * Returns the files the PDF embeds, every one, or the one $name gives.
     * Each comes back named after the PDF, then after itself, such as
     * invoice_factur-x.xml. It comes last in a list of stages.
     */
    public static function extractAttachments(string|null $name = null): self
    {
        return self::of('extract-attachments', ['name' => $name]);
    }

    /**
     * Returns the text the pages show as a .txt file, its pages separated by
     * a form feed, such as report.txt. $invisible adds the text layer a
     * searchable scan carries, $artifacts the headers, footers and page
     * numbers, and $annotations the text of annotations. It comes last in a
     * list of stages.
     */
    public static function extractText(
        string|Pages|null $pages = null,
        bool|null $invisible = null,
        bool|null $artifacts = null,
        bool|null $annotations = null,
    ): self {
        return self::of('extract-text', [
            'pages' => $pages,
            'invisible' => $invisible,
            'artifacts' => $artifacts,
            'annotations' => $annotations,
        ]);
    }

    /**
     * Returns the files the stage reads. Gotenberg expects them among the
     * uploaded files.
     *
     * @return list<Stream>
     */
    public function files(): array
    {
        return array_values(array_filter($this->options, static fn (mixed $value): bool => $value instanceof Stream));
    }

    /**
     * Returns the options as the form fields of the stage's own route:
     * strings and numbers as is, booleans as true or false, lists and objects
     * as JSON, and files by their names.
     *
     * @return array<string,string>
     */
    public function formFields(): array
    {
        return array_map(static function (mixed $value): string {
            return match (true) {
                $value instanceof Stream => $value->filename,
                $value instanceof BackedEnum => (string) $value->value,
                is_bool($value) => $value ? 'true' : 'false',
                is_array($value) => json_encode($value, JSON_THROW_ON_ERROR),
                default => (string) $value,
            };
        }, $this->options);
    }

    /** @return array<string,mixed> */
    public function jsonSerialize(): array
    {
        $options = array_map(
            static fn (mixed $value): mixed => $value instanceof Stream ? $value->filename : $value,
            $this->options,
        );

        return ['op' => $this->op] + $options;
    }
}
