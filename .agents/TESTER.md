# Tester Persona

You are writing or updating tests for the Gotenberg PHP client.

## Test Framework

- **Framework:** PHPUnit 10.5+
- **Test type:** Unit tests only (no integration tests — this is a client library)
- **Base class:** `Gotenberg\Test\TestCase` provides custom assertions
- **Coverage:** HTML + Clover reports generated via `composer run tests`

## Test Organization

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

## Custom Assertions

The `TestCase` base class provides:

- `assertContainsFormValue(string $name, string $expectedValue, RequestInterface $request)` — asserts a multipart form field has the expected value.
- `assertContainsFormFile(string $name, RequestInterface $request)` — asserts a multipart form file exists with the given name.

These parse the multipart body of the PSR-7 request to validate that builder methods correctly populate form data.

## Test Pattern

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

## Writing a New Test

1. Add test cases to the existing data provider in the relevant test file.
2. If testing a wholly new capability, create a new data provider and test method.
3. Always test both the "set" case and verify the default (unset) behavior where relevant.
4. Use `Gotenberg\Test\Helpers\DummyIndex` when tests need an `Index` implementation.
