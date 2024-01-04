<?php

declare(strict_types=1);

namespace Gotenberg;

use Gotenberg\Exceptions\NativeFunctionErrored;
use GuzzleHttp\Psr7\MultipartStream;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

use function json_encode;

trait MultipartFormDataModule
{
    use ApiModule;

    /** @var array<array<string,mixed>> */
    private array $multipartFormData = [];

    /**
     * Overrides the default UUID output filename.
     * Note: Gotenberg adds the file extension automatically; you don't have to
     * set it.
     */
    public function outputFilename(string $filename): self
    {
        $this->headers['Gotenberg-Output-Filename'] = $filename;

        return $this;
    }

    /**
     * Sets the callback and error callback that Gotenberg will use to send
     * respectively the output file and the error response.
     */
    public function webhook(string $url, string $errorUrl): self
    {
        $this->headers['Gotenberg-Webhook-Url']       = $url;
        $this->headers['Gotenberg-Webhook-Error-Url'] = $errorUrl;

        return $this;
    }

    /**
     * Overrides the default HTTP method that Gotenberg will use to call the
     * webhook.
     *
     * Either "POST", "PATCH", or "PUT" - default "POST".
     */
    public function webhookMethod(string $method): self
    {
        $this->headers['Gotenberg-Webhook-Method'] = $method;

        return $this;
    }

    /**
     * Overrides the default HTTP method that Gotenberg will use to call the
     * error webhook.
     *
     * Either "POST", "PATCH", or "PUT" - default "POST".
     */
    public function webhookErrorMethod(string $method): self
    {
        $this->headers['Gotenberg-Webhook-Error-Method'] = $method;

        return $this;
    }

    /**
     * Sets the extra HTTP headers that Gotenberg will send alongside the
     * request to the webhook and error webhook.
     *
     * @param array<string,string> $headers
     *
     * @throws NativeFunctionErrored
     */
    public function webhookExtraHttpHeaders(array $headers): self
    {
        $json = json_encode($headers);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        $this->headers['Gotenberg-Webhook-Extra-Http-Headers'] = $json;

        return $this;
    }

    protected function formValue(string $name, mixed $value): self
    {
        $this->multipartFormData[] = [
            'name' => $name,
            'contents' => $value,
        ];

        return $this;
    }

    protected function formFile(string $filename, StreamInterface $stream): void
    {
        $this->multipartFormData[] = [
            'name' => 'files',
            'filename' => $filename,
            'contents' => $stream,
        ];
    }

    protected function request(string $method = 'POST'): RequestInterface
    {
        $body = new MultipartStream($this->multipartFormData);

        $request = Psr17FactoryDiscovery::findRequestFactory()
            ->createRequest($method, $this->url . $this->endpoint)
            ->withHeader('Content-Type', 'multipart/form-data; boundary="' . $body->getBoundary() . '"')
            ->withBody($body);

        foreach ($this->headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        return $request;
    }
}
