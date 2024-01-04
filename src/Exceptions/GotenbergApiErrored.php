<?php

declare(strict_types=1);

namespace Gotenberg\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

final class GotenbergApiErroed extends Exception
{
    private ResponseInterface $response;

    public static function createFromResponse(ResponseInterface $response): self
    {
        $exception           = new self($response->getBody()->getContents(), $response->getStatusCode());
        $exception->response = $response;
        $exception->response->getBody()->rewind();

        return $exception;
    }

    public function getGotenbergTrace(string $header = 'Gotenberg-Trace'): string
    {
        return $this->response->getHeaderLine($header);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
