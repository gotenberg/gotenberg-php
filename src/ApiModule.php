<?php

declare(strict_types=1);

namespace Gotenberg;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestInterface;

use function rtrim;

trait ApiModule
{
    private string $url;
    private string $endpoint;
    /** @var array<string,string> */
    protected array $headers = [];

    public function __construct(string $baseUrl)
    {
        // The endpoints start with a slash.
        $this->url = rtrim($baseUrl, '/');
    }

    /**
     * Overrides the default UUID correlation ID that identifies a request in
     * Gotenberg's logs. Set $header if Gotenberg runs with a custom
     * --api-correlation-id-header.
     */
    public function correlationId(string $correlationId, string $header = 'Gotenberg-Trace'): static
    {
        $this->headers[$header] = $correlationId;

        return $this;
    }

    protected function request(string $method = 'GET'): RequestInterface
    {
        $request = Psr17FactoryDiscovery::findRequestFactory()
            ->createRequest($method, $this->url . $this->endpoint);

        foreach ($this->headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        return $request;
    }
}
