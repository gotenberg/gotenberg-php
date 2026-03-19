# Operational Guidelines for Gotenberg PHP Client

You are working on **gotenberg-php**, the official PHP client for [Gotenberg](https://gotenberg.dev), a Docker-based API for converting documents to PDF. This library is a production dependency for many PHP applications. Stability and backward compatibility are paramount.

## Mandatory Workflow

Every task MUST follow these five steps in order. Do not skip steps.

### Step 1 — Plan

Before writing any code, present:

1. **Problem statement** — What needs to change and why.
2. **Proposed solution** — How you intend to solve it.
3. **Alternatives** — Other approaches considered and why they were rejected (when pertinent).
4. **Scope** — List of files to touch.
5. **Testing strategy** — What tests to add or update.

**Wait for user approval before proceeding.**

### Step 2 — Implement

Code the plan. After implementation, verify the build passes:

```bash
composer run lint:fix
composer run lint
```

### Step 3 — Test

Write or update tests based on the testing strategy from Step 1.

```bash
composer run tests
```

### Step 4 — Review

Self-review the changes against the review checklist (see below). Present the review summary to the user.

### Step 5 — Commit

Only after explicit user approval. Follow Conventional Commits, stage specific files only.

---

## Core Principles

- **Backward compatibility is law.** Never rename or remove public methods, change method signatures, or alter default behavior unless explicitly instructed to perform a breaking change.
- **PSR compliance.** The client builds on PSR-7 (HTTP messages), PSR-17 (HTTP factories), and PSR-18 (HTTP client). Never introduce hard dependencies on specific HTTP implementations.
- **Fluent API consistency.** All configuration methods return `$this`. The builder pattern is the backbone of the public API — keep it uniform.
- **Strict types everywhere.** Every PHP file declares `strict_types=1`.

## Project Layout

```
src/
├── Gotenberg.php                    → Static factory (entry point)
├── ApiModule.php                    → Base trait: URL, endpoint, headers, request building
├── MultipartFormDataModule.php      → Trait: multipart form data, webhooks, downloads
├── Modules/
│   ├── Chromium.php                 → Chromium entry point (pdf/screenshot)
│   ├── ChromiumPdf.php              → Chromium PDF conversion builder
│   ├── ChromiumScreenshot.php       → Chromium screenshot builder
│   ├── ChromiumMultipartFormDataModule.php → Shared Chromium trait
│   ├── ChromiumCookie.php           → Cookie value object
│   ├── ChromiumEmulatedMediaFeatures.php  → Emulated media features value object
│   ├── LibreOffice.php              → LibreOffice conversion builder
│   └── PdfEngines.php               → PDF engines (merge, split, convert) builder
├── Exceptions/                      → Custom exceptions
├── Stream.php                       → File stream wrapper
├── SplitMode.php                    → Split mode enum
├── Index.php / HrtimeIndex.php      → Multipart name indexing
└── DownloadFrom.php                 → Download-from value object
tests/
├── TestCase.php                     → Base class with custom assertions
├── Modules/                         → Module-specific tests
└── ...                              → Other unit tests
```

## Quick Reference

- Fix coding style: `composer run lint:fix`
- Lint (phpcs + phpstan max level): `composer run lint`
- Run tests with coverage: `composer run tests`
- Run everything: `composer run all`
- Commits must follow [Conventional Commits](https://www.conventionalcommits.org/) (e.g., `feat(chromium): add emulatedMediaFeatures`)

## Codebase Navigation

- Start with `src/Gotenberg.php` for the public API surface, then follow into `src/Modules/` for builders.
- The trait hierarchy (`ApiModule` → `MultipartFormDataModule` → `ChromiumMultipartFormDataModule`) is key — read these to understand how form data is assembled.
- Tests use data providers and custom assertions (`assertContainsFormValue`, `assertContainsFormFile`) — read `tests/TestCase.php` first.
- Form field names must match Gotenberg's API exactly — refer to the [Gotenberg documentation](https://gotenberg.dev).

---

## Developer Reference

### Composer Scripts — the Build Interface

All verification tasks go through Composer scripts. Do not run tools directly unless debugging a specific issue.

| Command                   | Purpose                                              | When to use          |
| ------------------------- | ---------------------------------------------------- | -------------------- |
| `composer run lint:fix`   | Auto-fix coding standard violations (phpcbf)         | Before every commit  |
| `composer run lint`       | Run phpcs + phpstan at max level                     | Before every commit  |
| `composer run tests`      | Run PHPUnit with coverage                            | After code changes   |
| `composer run all`        | lint:fix + lint + tests in sequence                  | Before submitting PR |

### Architecture

The client uses a **fluent builder pattern** with traits for code reuse:

```
Gotenberg (static factory)
├── Chromium → ChromiumPdf / ChromiumScreenshot
├── LibreOffice
└── PdfEngines
```

**Trait hierarchy:**
- `ApiModule` — base trait: URL, endpoint, headers, trace ID, request building
- `MultipartFormDataModule` (uses `ApiModule`) — multipart form data, webhooks, download settings
- `ChromiumMultipartFormDataModule` (uses `MultipartFormDataModule`) — Chromium-specific options (cookies, media features, user agent, etc.)

**Request flow:**
1. Static factory: `Gotenberg::chromium($baseUrl)->pdf()`
2. Chain configuration methods (all return `$this`)
3. Terminal method creates the PSR-7 request: `.url()`, `.html()`, `.markdown()`, `.convert()`, etc.
4. Dispatch: `Gotenberg::send($request)` or `Gotenberg::save($request, $dir)`

### Coding Patterns

- **PSR compliance.** The client relies on PSR-7 (HTTP messages), PSR-17 (HTTP factories), and PSR-18 (HTTP client). Use `php-http/discovery` for auto-discovery.
- **Strict types.** Every file must declare `strict_types=1`.
- **Fluent API.** All configuration methods return `$this`. Keep this consistent.
- **Multipart form data.** Configuration is stored in `$this->multipartFormData` (array of `[name, value]` or `[name, value, filename]` entries). Use `$this->formValue()` and `$this->formFile()` helpers.
- **Coding standard.** Doctrine coding standard is enforced via phpcs/phpcbf. Follow it strictly.
- **Import ordering.** Use explicit `use function` and `use const` imports. Group class imports, function imports, and constant imports separately.

### Commit Convention

Commits must follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <description>
```

Common types: `feat`, `fix`, `refactor`, `test`, `docs`, `chore`. The scope should match the module or area of the change (e.g., `chromium`, `libreoffice`, `pdfengines`).

### Adding a New Chromium Option

1. Add the method to `ChromiumPdf.php`, `ChromiumScreenshot.php`, or the shared trait `ChromiumMultipartFormDataModule.php` (depending on scope).
2. The method should call `$this->formValue('formFieldName', $value)` or `$this->formFile('formFieldName', $stream)`.
3. Add a corresponding unit test with a data provider in the matching test file.

### Adding a New Module Option (LibreOffice / PdfEngines)

1. Add the method to the relevant module class.
2. Follow the same `formValue`/`formFile` pattern.
3. Add unit tests with data providers.

---

## Test Reference

### Test Framework

- **Framework:** PHPUnit 10.5+
- **Test type:** Unit tests only (no integration tests — this is a client library)
- **Base class:** `Gotenberg\Test\TestCase` provides custom assertions
- **Coverage:** HTML + Clover reports generated via `composer run tests`

### Test Organization

```
tests/
├── TestCase.php              → Base class with custom assertions
├── Helpers/                  → Helper mocks (DummyIndex.php, etc.)
├── Modules/
│   ├── ChromiumPdfTest.php
│   ├── ChromiumScreenshotTest.php
│   ├── LibreOfficeTest.php
│   └── PdfEnginesTest.php
├── ApiModuleTest.php
├── MultipartFormDataModuleTest.php
├── GotenbergTest.php
├── StreamTest.php
├── HrtimeIndexTest.php
└── SplitModeTest.php
```

### Custom Assertions

The `TestCase` base class provides:

- `assertContainsFormValue(string $name, string $expectedValue, RequestInterface $request)` — asserts a multipart form field has the expected value.
- `assertContainsFormFile(string $name, RequestInterface $request)` — asserts a multipart form file exists with the given name.

These parse the multipart body of the PSR-7 request to validate that builder methods correctly populate form data.

### Test Pattern

Tests follow a **data provider pattern**:

1. A static data provider method returns an array of test cases, each containing:
   - A description string
   - A `RequestInterface` built via the fluent API
   - Expected form values/files to assert
2. The test method receives these parameters and runs assertions.

Example structure:
```php
public static function dataProvider(): array
{
    return [
        'description of test case' => [
            Gotenberg::chromium('http://localhost:3000')
                ->pdf()
                ->someOption('value')
                ->url('https://example.com'),
            ['formFieldName' => 'value'],
        ],
    ];
}

#[DataProvider('dataProvider')]
public function testSomeFeature(RequestInterface $request, array $expected): void
{
    foreach ($expected as $name => $value) {
        $this->assertContainsFormValue($name, $value, $request);
    }
}
```

### Writing a New Test

1. Add test cases to the existing data provider in the relevant test file.
2. If testing a wholly new capability, create a new data provider and test method.
3. Always test both the "set" case and verify the default (unset) behavior where relevant.
4. Use `Gotenberg\Test\Helpers\DummyIndex` when tests need an `Index` implementation.

---

## Review Checklist

### Backward Compatibility

- [ ] No existing public methods renamed or removed
- [ ] No existing method signatures changed (parameter types, return types)
- [ ] No changes to default behavior (form field defaults, endpoint paths)
- [ ] No breaking changes to the fluent API chain

If any of these are violated, the change **must** be flagged as a breaking change.

### Coding Standards

- **Doctrine coding standard** is enforced via phpcs (`squizlabs/php_codesniffer` + `doctrine/coding-standard`).
- **PHPStan at max level** — zero errors permitted.
- `declare(strict_types=1)` in every PHP file.
- Explicit `use function` and `use const` imports (no inline fully-qualified calls).

Run `composer run lint` to verify. Zero errors are permitted.

### Code Quality

- All configuration methods return `$this` for fluent chaining.
- Form values use the correct Gotenberg API field names (check against the [Gotenberg docs](https://gotenberg.dev)).
- PSR-7/PSR-17/PSR-18 interfaces are used — no hard dependency on a specific HTTP client.
- No side effects in builder methods beyond populating `$multipartFormData` or `$headers`.

### Test Coverage

- Every new public method must have corresponding test cases.
- Tests use data providers and the custom `assertContainsFormValue`/`assertContainsFormFile` assertions.
- `composer run tests` must pass with no failures.

### Definition of Done

A change is ready to merge only when:

1. Coding standards pass: `composer run lint`
2. All tests pass: `composer run tests`
3. All new public methods have tests
4. The fluent API is consistent with existing patterns
5. Commits follow Conventional Commits format
