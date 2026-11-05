<?php

declare(strict_types=1);

namespace Gotenberg;

use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Gotenberg\Modules\Chromium;
use Gotenberg\Modules\LibreOffice;
use Gotenberg\Modules\Pdf;
use GuzzleHttp\Psr7\StreamWrapper;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function basename;
use function file_put_contents;
use function preg_match;
use function rawurldecode;

use const DIRECTORY_SEPARATOR;

final class Gotenberg
{
    public static function chromium(string $baseUrl): Chromium
    {
        return new Chromium($baseUrl);
    }

    public static function libreOffice(string $baseUrl): LibreOffice
    {
        return new LibreOffice($baseUrl);
    }

    public static function pdf(string $baseUrl): Pdf
    {
        return new Pdf($baseUrl);
    }

    /**
     * Sends a request to Gotenberg and throws an exception if the status code
     * is not 2xx.
     *
     * @throws GotenbergApiErrored
     */
    public static function send(RequestInterface $request, ClientInterface|null $client = null): ResponseInterface
    {
        $client ??= Psr18ClientDiscovery::find();
        $response = $client->sendRequest($request);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw GotenbergApiErrored::createFromResponse($response);
        }

        return $response;
    }

    /**
     * Handles a request to Gotenberg and saves the output file if any.
     * On success, returns the filename based on the 'Content-Disposition' header.
     *
     * @throws GotenbergApiErrored
     * @throws NoOutputFileInResponse
     * @throws NativeFunctionErrored
     */
    public static function save(RequestInterface $request, string $dirPath, ClientInterface|null $client = null): string
    {
        $response = self::send($request, $client);
        $filename = self::filename($response->getHeaderLine('Content-Disposition'));

        if ($filename === null) {
            throw new NoOutputFileInResponse();
        }

        // Streams the body to disk in chunks, without loading it into memory.
        if (file_put_contents($dirPath . DIRECTORY_SEPARATOR . $filename, StreamWrapper::getResource($response->getBody())) === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        return $filename;
    }

    /**
     * Returns the file name a Content-Disposition header carries, preferring
     * the RFC 6266 filename* parameter over its ASCII fallback. The name loses
     * any directory part, so that it cannot escape the saving directory.
     */
    private static function filename(string $contentDisposition): string|null
    {
        if (preg_match("/filename\*=UTF-8''([^;\s]+)/i", $contentDisposition, $matches)) {
            $filename = rawurldecode($matches[1]);
        } elseif (preg_match('/filename="(.+?)"/', $contentDisposition, $matches)) {
            $filename = $matches[1];
        } elseif (preg_match('/filename=([^;\s]+)/', $contentDisposition, $matches)) {
            $filename = $matches[1];
        } else {
            return null;
        }

        $filename = basename($filename);

        return $filename === '' || $filename === '.' || $filename === '..' ? null : $filename;
    }
}
