<?php

declare(strict_types=1);

use Gotenberg\DownloadFrom;
use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Test\DummyMultipartFormDataModule;

it(
    'creates a valid request with given HTTP headers',
    function (): void {
        $dummy   = new DummyMultipartFormDataModule('https://my.url/');
        $request = $dummy
            ->outputFilename('my_filename')
            ->downloadFrom([
                new DownloadFrom('https://my.url/my_filename'),
                new DownloadFrom('https://my.url/my_filename_2', ['X-Header' => 'value']),
            ])
            ->webhook('https://my.webhook.url', 'https://my.webhook.error.url')
            ->webhookMethod('POST')
            ->webhookErrorMethod('PUT')
            ->webhookExtraHttpHeaders([
                'My-Webhook-Http-Header' => 'HTTP header content',
                'My-Second-Webhook-Http-Header' => 'Second HTTP header content',
            ])
            ->build();

        expect($request->getHeader('Gotenberg-Output-Filename'))->toMatchArray(['my_filename']);

        expect(sanitize($request->getBody()->getContents()))->toContainFormValue('downloadFrom', '[{"url":"https:\/\/my.url\/my_filename"},{"url":"https:\/\/my.url\/my_filename_2","extraHttpHeaders":{"X-Header":"value"}}]');

        expect($request->getHeader('Gotenberg-Webhook-Url'))->toMatchArray(['https://my.webhook.url']);
        expect($request->getHeader('Gotenberg-Webhook-Error-Url'))->toMatchArray(['https://my.webhook.error.url']);
        expect($request->getHeader('Gotenberg-Webhook-Method'))->toMatchArray(['POST']);
        expect($request->getHeader('Gotenberg-Webhook-Error-Method'))->toMatchArray(['PUT']);

        $json = json_encode([
            'My-Webhook-Http-Header' => 'HTTP header content',
            'My-Second-Webhook-Http-Header' => 'Second HTTP header content',
        ]);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        expect($request->getHeader('Gotenberg-Webhook-Extra-Http-Headers'))->toMatchArray([$json]);
    },
);
