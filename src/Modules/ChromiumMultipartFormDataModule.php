<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\MultipartFormDataModule;
use Gotenberg\Stream;

use function json_encode;

trait ChromiumMultipartFormDataModule
{
    use MultipartFormDataModule;

    /**
     * Hides default white background and allows generating PDFs with
     * transparency.
     */
    public function omitBackground(): self
    {
        $this->formValue('omitBackground', true);

        return $this;
    }

    /**
     * Sets the duration (i.e., "1s", "2ms", etc.) to wait when loading an HTML
     * document before converting it to PDF.
     */
    public function waitDelay(string $delay): self
    {
        $this->formValue('waitDelay', $delay);

        return $this;
    }

    /**
     * Sets the JavaScript expression to wait before converting an HTML
     * document to PDF until it returns true.
     *
     * For instance: "window.status === 'ready'".
     */
    public function waitForExpression(string $expression): self
    {
        $this->formValue('waitForExpression', $expression);

        return $this;
    }

    /**
     * Forces Chromium to emulate the media type "print".
     */
    public function emulatePrintMediaType(): self
    {
        $this->formValue('emulatedMediaType', 'print');

        return $this;
    }

    /**
     * Forces Chromium to emulate the media type "screen".
     */
    public function emulateScreenMediaType(): self
    {
        $this->formValue('emulatedMediaType', 'screen');

        return $this;
    }

    /**
     * Sets extra HTTP headers that Chromium will send when loading the HTML
     * document.
     *
     * @param array<string,string> $headers
     *
     * @throws NativeFunctionErrored
     */
    public function extraHttpHeaders(array $headers): self
    {
        $json = json_encode($headers);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        $this->formValue('extraHttpHeaders', $json);

        return $this;
    }

    /**
     * Forces Gotenberg to return a 409 Conflict response if the HTTP status
     * code from the main page is not acceptable.
     *
     * @param int[] $statusCodes
     *
     * @throws NativeFunctionErrored
     */
    public function failOnHttpStatusCodes(array $statusCodes): self
    {
        $json = json_encode($statusCodes);
        if ($json === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        $this->formValue('failOnHttpStatusCodes', $json);

        return $this;
    }

    /**
     * Forces Gotenberg to return a 409 Conflict response if there are
     * exceptions in the Chromium console.
     */
    public function failOnConsoleExceptions(): self
    {
        $this->formValue('failOnConsoleExceptions', true);

        return $this;
    }

    /**
     * Tells Chromium to not wait for its network to be idle.
     */
    public function skipNetworkIdleEvent(): self
    {
        $this->formValue('skipNetworkIdleEvent', true);

        return $this;
    }

    /**
     * Sets the additional files, like images, fonts, stylesheets, and so on.
     */
    public function assets(Stream ...$assets): self
    {
        foreach ($assets as $asset) {
            $this->formFile($asset->getFilename(), $asset->getStream());
        }

        return $this;
    }
}
