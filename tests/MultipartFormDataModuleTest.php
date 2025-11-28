<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\DownloadFrom;
use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Test\Helpers\Dummies\DummyMultipartFormDataModule;
use PHPUnit\Framework\Attributes\Test;

use function json_encode;

final class MultipartFormDataModuleTest extends TestCase
{
    #[Test]
    public function it_creates_a_valid_request_with_given_http_headers(): void
    {
        $dummy   = new DummyMultipartFormDataModule('https://my.url/');
        $request = $dummy
            ->outputFilename('my_filename')
            ->downloadFrom([
                new DownloadFrom('https://my.url/my_filename'),
                new DownloadFrom('https://my.url/my_filename_2', ['X-Header' => 'value'], true),
            ])
            ->webhook('https://my.webhook.url', 'https://my.webhook.error.url')
            ->webhookMethod('POST')
            ->webhookErrorMethod('PUT')
            ->webhookExtraHttpHeaders([
                'My-Webhook-Http-Header'        => 'HTTP header content',
                'My-Second-Webhook-Http-Header' => 'Second HTTP header content',
            ])
            ->build();

        // Assert Output Filename Header
        $this->assertSame(['my_filename'], $request->getHeader('Gotenberg-Output-Filename'));

        // Assert DownloadFrom Form Value
        $body         = $this->sanitize($request->getBody()->getContents());
        $expectedJson = '[{"url":"https:\/\/my.url\/my_filename","embedded":false},{"url":"https:\/\/my.url\/my_filename_2","embedded":true,"extraHttpHeaders":{"X-Header":"value"}}]';

        $this->assertContainsFormValue($body, 'downloadFrom', $expectedJson);

        // Assert Webhook Headers
        $this->assertSame(['https://my.webhook.url'], $request->getHeader('Gotenberg-Webhook-Url'));
        $this->assertSame(['https://my.webhook.error.url'], $request->getHeader('Gotenberg-Webhook-Error-Url'));
        $this->assertSame(['POST'], $request->getHeader('Gotenberg-Webhook-Method'));
        $this->assertSame(['PUT'], $request->getHeader('Gotenberg-Webhook-Error-Method'));

        // Assert Webhook Extra Http Headers
        $webhookHeaders = [
            'My-Webhook-Http-Header'        => 'HTTP header content',
            'My-Second-Webhook-Http-Header' => 'Second HTTP header content',
        ];

        $json = json_encode($webhookHeaders);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        $this->assertSame([$json], $request->getHeader('Gotenberg-Webhook-Extra-Http-Headers'));
    }
}
