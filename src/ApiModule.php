<?php

declare(strict_types=1);

namespace Gotenberg;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

use function mb_substr;

trait ApiModule
{
    private string $url;
    private string $endpoint;
    /** @var array<string,string> */
    protected array $headers = [];

    public function __construct(string $baseUrl)
    {
        if (mb_substr($baseUrl, -1) === '/') {
            // We remove the trailing slash as the endpoints start with a slash.
            $this->url = mb_substr($baseUrl, 0, -1);

            return;
        }

        $this->url = $baseUrl;
    }

    /**
     * Adds or updates a header to send with the request to Gotenberg.
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Overrides the default UUID trace, or request ID, that identifies a
     * request in Gotenberg's logs.
     */
    public function trace(string $trace, string $header = 'Gotenberg-Trace'): self
    {
        return $this->setHeader($header, $trace);
    }

    protected function request(string $method, ?StreamInterface $body = null): RequestInterface
    {
        $request = Psr17FactoryDiscovery::findRequestFactory()
            ->createRequest($method, $this->url . $this->endpoint);

        if ($body !== null) {
            $request->withBody($body);
        }

        foreach ($this->headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        return $request;
    }
}
