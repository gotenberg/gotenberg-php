<?php

declare(strict_types=1);

namespace Gotenberg;

use Gotenberg\Exceptions\GotenbergApiErroed;
use Gotenberg\Exceptions\NativeFunctionErroed;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Gotenberg\Modules\Chromium;
use Gotenberg\Modules\LibreOffice;
use Gotenberg\Modules\PdfEngines;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function count;
use function fclose;
use function fopen;
use function fwrite;
use function preg_match;

use const DIRECTORY_SEPARATOR;

class Gotenberg
{
    public static function chromium(string $baseUrl): Chromium
    {
        return new Chromium($baseUrl);
    }

    public static function libreOffice(string $baseUrl): LibreOffice
    {
        return new LibreOffice($baseUrl);
    }

    public static function pdfEngines(string $baseUrl): PdfEngines
    {
        return new PdfEngines($baseUrl);
    }

    /**
     * Sends a request to Gotenberg and throws an exception if the status code
     * is not 200.
     *
     * @throws GotenbergApiErroed
     */
    public static function send(RequestInterface $request, ?ClientInterface $client = null): ResponseInterface
    {
        $client   = $client ?: Psr18ClientDiscovery::find();
        $response = $client->sendRequest($request);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw GotenbergApiErroed::createFromResponse($response);
        }

        return $response;
    }

    /**
     * Handles a request to Gotenberg and saves the output file if any.
     * On success, returns the filename based on the 'Content-Disposition' header.
     *
     * @throws GotenbergApiErroed
     * @throws NoOutputFileInResponse
     * @throws NativeFunctionErroed
     */
    public static function save(RequestInterface $request, string $dirPath, ?ClientInterface $client = null): string
    {
        $response = self::send($request, $client);

        $contentDisposition = $response->getHeader('Content-Disposition');
        if (count($contentDisposition) === 0) {
            throw new NoOutputFileInResponse();
        }

        $filename = false;
        foreach ($contentDisposition as $value) {
            if (preg_match('/filename="(.+?)"/', $value, $matches)) {
                $filename = $matches[1];
                break;
            }

            if (preg_match('/filename=([^; ]+)/', $value, $matches)) {
                $filename = $matches[1];
                break;
            }
        }

        if ($filename === false) {
            throw new NoOutputFileInResponse();
        }

        $file = fopen($dirPath . DIRECTORY_SEPARATOR . $filename, 'w');
        if ($file === false) {
            throw NativeFunctionErroed::createFromLastPhpError();
        }

        if (fwrite($file, $response->getBody()->getContents()) === false) {
            throw NativeFunctionErroed::createFromLastPhpError();
        }

        if (fclose($file) === false) {
            throw NativeFunctionErroed::createFromLastPhpError();
        }

        return $filename;
    }
}
