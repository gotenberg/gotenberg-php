<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Gotenberg\Gotenberg;
use Gotenberg\Test\Helpers\Dummies\DummyClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;

use function file_get_contents;
use function ini_set;
use function sys_get_temp_dir;
use function unlink;

use const DIRECTORY_SEPARATOR;

final class GotenbergTest extends TestCase
{
    #[Test]
    public function it_sends_a_request(): void
    {
        $response = new Response(200, ['Gotenberg-Trace' => 'debug']);
        $client   = new DummyClient($response);

        $this->assertSame($response, Gotenberg::send(new Request('POST', 'https://my.url'), $client));
    }

    #[Test]
    #[DataProvider('provideTraceData')]
    public function it_sends_a_request_and_throws_an_exception_if_response_is_not_2xx(bool $withTrace): void
    {
        $response = new Response(400, $withTrace ? ['Gotenberg-Trace' => 'debug'] : [], 'Bad Request');
        $client   = new DummyClient($response);

        try {
            Gotenberg::send(new Request('POST', 'https://my.url'), $client);
            $this->fail('Exception was expected but not thrown.');
        } catch (GotenbergApiErrored $e) {
            $this->assertSame(400, $e->getCode());
            $this->assertSame('Bad Request', $e->getMessage());
            $this->assertSame($withTrace ? 'debug' : '', $e->getCorrelationId());
            $this->assertSame($response, $e->getResponse());
        }
    }

    /** @return array<string, array{bool}> */
    public static function provideTraceData(): array
    {
        return [
            'with trace'    => [true],
            'without trace' => [false],
        ];
    }

    #[Test]
    #[DataProvider('provideFilenameData')]
    public function it_saves_the_output_file(string $contentDisposition, string $expectedFilename): void
    {
        $response = new Response(200, ['Content-Disposition' => $contentDisposition], 'PDF content');
        $client   = new DummyClient($response);

        $tempDir  = sys_get_temp_dir();
        $filename = Gotenberg::save(new Request('POST', 'https://my.url'), $tempDir, $client);
        $filePath = $tempDir . DIRECTORY_SEPARATOR . $expectedFilename;

        $this->assertSame($expectedFilename, $filename);
        $this->assertSame('PDF content', file_get_contents($filePath));
        $this->assertTrue(unlink($filePath));
    }

    /** @return array<string, array{string, string}> */
    public static function provideFilenameData(): array
    {
        return [
            'unquoted'           => ['attachment; filename=my.pdf', 'my.pdf'],
            'quoted'             => ['attachment; filename="my file.pdf"', 'my file.pdf'],
            'rfc 6266 preferred' => ["attachment; filename=\"facture-ete.pdf\"; filename*=UTF-8''facture-%C3%A9t%C3%A9.pdf", 'facture-été.pdf'],
            'directory dropped'  => ['attachment; filename="../../my.pdf"', 'my.pdf'],
        ];
    }

    #[Test]
    #[DataProvider('provideContentDispositionData')]
    public function it_throws_an_exception_if_there_is_no_attachment(string|null $contentDisposition): void
    {
        $response = new Response(200, $contentDisposition === null ? [] : ['Content-Disposition' => $contentDisposition]);
        $client   = new DummyClient($response);

        $this->expectException(NoOutputFileInResponse::class);

        Gotenberg::save(new Request('POST', 'https://my.url'), sys_get_temp_dir(), $client);
    }

    #[Test]
    #[WithoutErrorHandler]
    public function it_throws_an_exception_if_the_output_file_cannot_be_opened(): void
    {
        $response = new Response(200, ['Content-Disposition' => 'attachment; filename=my.pdf'], 'PDF content');
        $client   = new DummyClient($response);

        $this->expectException(NativeFunctionErrored::class);

        // Without PHPUnit's handler, PHP prints the fopen warning itself.
        $displayErrors = ini_set('display_errors', '0');
        try {
            Gotenberg::save(new Request('POST', 'https://my.url'), sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'missing-dir', $client);
        } finally {
            ini_set('display_errors', (string) $displayErrors);
        }
    }

    /** @return array<string, array{0: string|null}> */
    public static function provideContentDispositionData(): array
    {
        return [
            'without content disposition' => [null],
            'with content disposition'    => ['no attachment'],
            'with a dot dot filename'     => ['attachment; filename=..'],
        ];
    }
}
