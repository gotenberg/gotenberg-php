<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Gotenberg\Gotenberg;
use Gotenberg\Test\Helpers\Dummies\DummyClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

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

        $response = Gotenberg::send(new Request('POST', 'https://my.url'), $client);

        $this->assertNotNull($response);
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
            $this->assertSame($withTrace ? 'debug' : '', $e->getGotenbergTrace());
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
    public function it_saves_the_output_file(): void
    {
        $response = new Response(200, ['Content-Disposition' => 'attachment; filename=my.pdf']);
        $client   = new DummyClient($response);

        $tempDir  = sys_get_temp_dir();
        $filename = Gotenberg::save(new Request('POST', 'https://my.url'), $tempDir, $client);

        $filePath = $tempDir . DIRECTORY_SEPARATOR . 'my.pdf';

        // verify file exists and delete it
        $this->assertTrue(unlink($filePath));
        $this->assertSame('my.pdf', $filename);
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

    /** @return array<string, array{0: string|null}> */
    public static function provideContentDispositionData(): array
    {
        return [
            'without content disposition' => [null],
            'with content disposition'    => ['no attachment'],
        ];
    }
}
