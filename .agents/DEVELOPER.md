# Developer Persona

You are implementing features, fixing bugs, or refactoring code in the Gotenberg PHP client.

## Composer Scripts — the Build Interface

All verification tasks go through Composer scripts. Do not run tools directly unless debugging a specific issue.

| Command                   | Purpose                                              | When to use          |
| ------------------------- | ---------------------------------------------------- | -------------------- |
| `composer run lint:fix`   | Auto-fix coding standard violations (phpcbf)         | Before every commit  |
| `composer run lint`       | Run phpcs + phpstan at max level                     | Before every commit  |
| `composer run tests`      | Run PHPUnit with coverage                            | After code changes   |
| `composer run all`        | lint:fix + lint + tests in sequence                  | Before submitting PR |

## Architecture

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

## Coding Patterns

- **PSR compliance.** The client relies on PSR-7 (HTTP messages), PSR-17 (HTTP factories), and PSR-18 (HTTP client). Use `php-http/discovery` for auto-discovery.
- **Strict types.** Every file must declare `strict_types=1`.
- **Fluent API.** All configuration methods return `$this`. Keep this consistent.
- **Multipart form data.** Configuration is stored in `$this->multipartFormData` (array of `[name, value]` or `[name, value, filename]` entries). Use `$this->formValue()` and `$this->formFile()` helpers.
- **Coding standard.** Doctrine coding standard is enforced via phpcs/phpcbf. Follow it strictly.
- **Import ordering.** Use explicit `use function` and `use const` imports. Group class imports, function imports, and constant imports separately.

## Commit Convention

Commits must follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <description>
```

Common types: `feat`, `fix`, `refactor`, `test`, `docs`, `chore`. The scope should match the module or area of the change (e.g., `chromium`, `libreoffice`, `pdfengines`).

## Adding a New Chromium Option

1. Add the method to `ChromiumPdf.php`, `ChromiumScreenshot.php`, or the shared trait `ChromiumMultipartFormDataModule.php` (depending on scope).
2. The method should call `$this->formValue('formFieldName', $value)` or `$this->formFile('formFieldName', $stream)`.
3. Add a corresponding unit test with a data provider in the matching test file.

## Adding a New Module Option (LibreOffice / PdfEngines)

1. Add the method to the relevant module class.
2. Follow the same `formValue`/`formFile` pattern.
3. Add unit tests with data providers.
