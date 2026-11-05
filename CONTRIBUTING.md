# Contributing to Gotenberg PHP Client

Official PHP client for [Gotenberg](https://gotenberg.dev), a Docker-based API for converting documents to PDF. Two rules override everything else: **backward compatibility** (never rename or remove public methods or change method signatures without discussion) and **PSR compliance** (build on PSR-7, PSR-17, PSR-18, never introduce hard dependencies on specific HTTP implementations).

- PHP 8.3+ (see `composer.json`)
- Composer

## Quick start

```bash
composer install
composer run lint:fix  # Auto-fix coding standard violations
composer run lint      # phpcs + phpstan at max level (zero errors permitted)
composer run tests     # PHPUnit with coverage
composer run all       # lint:fix + lint + tests
```

Run all Composer commands inside this Docker container:

```bash
docker run --rm -it \
    -e PHP_EXTENSION_XDEBUG=1 \
    -v $(PWD):/usr/src/app/ \
    thecodingmachine/php:8.3-v4-cli \
    bash -c "composer run all"
```

## Project layout

```
src/
  Gotenberg.php                  Static factory (entry point)
  ApiModule.php                  Base trait: URL, endpoint, headers, request building
  MultipartFormDataModule.php    Trait: multipart form data, webhooks, downloads, archives
  StagesModule.php               Trait: stages, merge ordering (Index)
  Stage.php                      Stage value object: one named constructor per Gotenberg stage
  Profile.php, PaperSize.php…    Backed enums for the parameters with a fixed set of values
  Modules/
    Chromium.php                 Chromium entry point (pdf/screenshot)
    ChromiumPdf.php              Chromium PDF conversion builder
    ChromiumScreenshot.php       Chromium screenshot builder
    ChromiumMultipartFormDataModule.php  Shared Chromium trait
    ChromiumCookie.php           Cookie value object
    LibreOffice.php              LibreOffice conversion builder
    Pdf.php                      PDF builder: pipe, single-stage routes, read routes
  Exceptions/                    Custom exceptions
  Stream.php                     File stream wrapper
  DownloadFrom.php               Download-from value object
tests/
  TestCase.php                   Base class with custom assertions
  Modules/                       Module-specific tests
```

Start with `src/Gotenberg.php` for the public API surface, then follow into `src/Modules/` for builders. The trait hierarchy (`ApiModule` -> `MultipartFormDataModule` -> `ChromiumMultipartFormDataModule`) is key to understanding how form data is assembled.

## Coding rules

### Fluent API

All configuration methods return `$this`, typed `static`. The builder pattern is the backbone of the public API. Keep it uniform.

### Strict types

Every PHP file declares `strict_types=1`.

### Multipart form data

Configuration is stored in `$this->multipartFormData` (array of `[name, value]` or `[name, value, filename]` entries). Use `$this->formValue()` and `$this->formFile()` helpers. `formValue()` sends booleans as `true`/`false`; encode lists and objects with `json_encode(..., JSON_THROW_ON_ERROR)`.

### Known values

A parameter with a fixed set of values takes a backed string enum, one file per enum in `src/` (`Profile`, `PaperSize`, `Magnification`…), with PascalCase cases. Send `$enum->value`; `json_encode` serializes enums inside lists and stage options on its own. A parameter that also takes free text, such as a page selection, takes a union (`string|Pages`).

### Coding standard

Doctrine coding standard enforced via phpcs/phpcbf. Follow it strictly. Use explicit `use function` and `use const` imports. Group class imports, function imports, and constant imports separately.

### Adding a new option

For Chromium: add the method to `ChromiumPdf.php`, `ChromiumScreenshot.php`, or the shared trait `ChromiumMultipartFormDataModule.php` depending on scope. Call `$this->formValue('formFieldName', $value)` or `$this->formFile('formFieldName', $stream)`. Add a corresponding unit test with a data provider.

For LibreOffice or Pdf: add the method to the relevant module class. Same `formValue`/`formFile` pattern, same test requirement.

For a new stage: add a named constructor to `Stage.php`, its options keyed by their Gotenberg names, `null` for "keep the default", and a `Stream` for a file option. `stages()` and `Pdf::run()` pick it up; add a case to `StageTest`.

## Testing

Unit tests only (no integration tests, this is a client library). PHPUnit 12.4+. Base class `Gotenberg\Test\TestCase` provides custom assertions:

- `assertRequest(RequestInterface $request, string $endpoint, array $values, array $files)`: asserts the endpoint, the form values (`[name, value]`) and the form files (`[field, filename, content, content type]`).
- `assertContainsFormValue()` and `assertContainsFormFile()`: the single checks `assertRequest()` runs.

Tests follow a data provider pattern: a static method returns an array of test cases (description, `RequestInterface` built via the fluent API, expected form values/files), and the test method runs assertions against each.

Form field names must match Gotenberg's API exactly. Cross-reference with the [Gotenberg documentation](https://gotenberg.dev).

## Pull requests

Plan non-trivial changes before coding. Present what needs to change, why, which files are affected, and what tests to add.

### Checklist

- [ ] No existing public methods renamed or removed
- [ ] No existing method signatures changed (parameter types, return types)
- [ ] No changes to default behavior (form field defaults, endpoint paths)
- [ ] No breaking changes to the fluent API chain
- [ ] All configuration methods return `$this`
- [ ] Form values use the correct Gotenberg API field names
- [ ] PSR-7/PSR-17/PSR-18 interfaces used, no hard HTTP client dependency
- [ ] `declare(strict_types=1)` in every PHP file
- [ ] Doctrine coding standard: `composer run lint` passes with zero errors
- [ ] Every new public method has corresponding test cases with data providers
- [ ] `composer run tests` passes with no failures

### Commits

[Conventional Commits](https://www.conventionalcommits.org/): `feat:`, `fix:`, `refactor:`, `test:`, `docs:`, `chore:`. Scope should match the module (e.g., `chromium`, `libreoffice`, `pdf`, `stage`).

Stage specific files. Never `git add -A` or `git add .`.
