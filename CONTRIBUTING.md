# Contributing to Gotenberg PHP Client

Thank you for your interest in contributing to the Gotenberg PHP client! This guide will help you get started.

## Before You Start

Please read the [AGENTS.md](AGENTS.md) file — it describes the core principles, project layout, and development standards that all contributions must follow. Even though it is written for AI agents, the same rules apply to human contributors.

## Getting Started

### Prerequisites

- PHP 8.1+ (see `composer.json` for supported versions)
- Composer

### Install Dependencies

```bash
composer install
```

### Development Loop

```bash
# Write your code, then:
composer run lint:fix  # Auto-fix coding standard violations
composer run lint      # Run phpcs + phpstan at max level (zero errors permitted)
composer run tests     # Run PHPUnit with coverage
```

Or run everything at once:

```bash
composer run all       # lint:fix + lint + tests
```

## Submitting a Pull Request

Before opening a PR, verify:

1. Coding standards pass: `composer run lint`
2. All tests pass: `composer run tests`
3. All new public methods have corresponding tests
4. The fluent API is consistent with existing patterns

### Guidelines

- **Conventional Commits.** Commit messages must follow the [Conventional Commits](https://www.conventionalcommits.org/) specification (e.g., `feat(chromium): add emulatedMediaFeatures`, `fix(pdfengines): handle empty merge`).
- **One thing per PR.** Keep features, bug fixes, and refactoring in separate PRs.
- **Backward compatibility matters.** Do not rename or remove existing public methods or change method signatures without discussion.
- **Tests first.** When adding a feature, start by writing the test cases with data providers.
- **PSR compliance.** Never introduce hard dependencies on specific HTTP client implementations.
