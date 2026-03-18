# Operational Guidelines for Gotenberg PHP Client

You are working on **gotenberg-php**, the official PHP client for [Gotenberg](https://gotenberg.dev), a Docker-based API for converting documents to PDF. This library is a production dependency for many PHP applications. Stability and backward compatibility are paramount.

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

## Persona Selection (MANDATORY)

Before starting any task, you MUST read the appropriate persona file from `.agents/` based on what is being asked. This is not optional — the persona contains critical context you need.

| Task type                                                    | Persona to load                                | Trigger keywords / signals                                                   |
| ------------------------------------------------------------ | ---------------------------------------------- | ---------------------------------------------------------------------------- |
| Writing or modifying code (features, bug fixes, refactoring) | [`.agents/DEVELOPER.md`](.agents/DEVELOPER.md) | "add", "fix", "implement", "refactor", "change", "update", writing any file |
| Writing or updating tests                                    | [`.agents/TESTER.md`](.agents/TESTER.md)       | "test", "coverage", `Test.php` files, data providers                         |
| Reviewing code or PRs                                       | [`.agents/REVIEWER.md`](.agents/REVIEWER.md)   | "review", "check", "audit", PR URLs, reviewing diffs                         |

If a task spans multiple concerns (e.g., implementing a feature AND writing tests), load ALL relevant personas.
