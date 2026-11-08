<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\DownloadFrom;
use Gotenberg\Stream;
use Gotenberg\Test\Helpers\Dummies\DummyMultipartFormDataModule;
use Gotenberg\WebhookMethod;
use PHPUnit\Framework\Attributes\Test;

final class MultipartFormDataModuleTest extends TestCase
{
    #[Test]
    public function it_creates_a_valid_request_with_the_common_fields_and_headers(): void
    {
        $dummy   = new DummyMultipartFormDataModule('https://my.url/');
        $request = $dummy
            ->outputFilename('my_filename')
            ->downloadFrom(
                new DownloadFrom('https://my.url/my_filename'),
                new DownloadFrom('https://my.url/my_filename_2', ['X-Header' => 'value']),
                new DownloadFrom('https://my.url/download?id=42', filename: 'my_filename_3.docx'),
            )
            ->archives(Stream::string('site.zip', 'zip content'))
            ->webhook('https://my.webhook.url', 'https://my.webhook.events.url')
            ->webhookMethod(WebhookMethod::Put)
            ->webhookExtraHttpHeaders(['My-Webhook-Http-Header' => 'HTTP header content'])
            ->build();

        $this->assertStringStartsWith('multipart/form-data; boundary=', $request->getHeaderLine('Content-Type'));
        $this->assertSame(['my_filename'], $request->getHeader('Gotenberg-Output-Filename'));
        $this->assertSame(['https://my.webhook.url'], $request->getHeader('Gotenberg-Webhook-Url'));
        $this->assertSame(['https://my.webhook.events.url'], $request->getHeader('Gotenberg-Webhook-Events-Url'));
        $this->assertSame(['PUT'], $request->getHeader('Gotenberg-Webhook-Method'));
        $this->assertSame(['{"My-Webhook-Http-Header":"HTTP header content"}'], $request->getHeader('Gotenberg-Webhook-Extra-Http-Headers'));

        $this->assertRequest(
            $request,
            '/foo',
            [['downloadFrom', '[{"url":"https:\/\/my.url\/my_filename"},{"url":"https:\/\/my.url\/my_filename_2","extraHttpHeaders":{"X-Header":"value"}},{"url":"https:\/\/my.url\/download?id=42","filename":"my_filename_3.docx"}]']],
            [['archives', 'site.zip', 'zip content']],
        );
    }
}
