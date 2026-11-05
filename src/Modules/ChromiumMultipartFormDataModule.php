<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\ConsoleMessageType;
use Gotenberg\MultipartFormDataModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

use function array_values;
use function json_encode;

use const JSON_THROW_ON_ERROR;

trait ChromiumMultipartFormDataModule
{
    use MultipartFormDataModule;

    /**
     * Hides the default white background and allows transparency.
     */
    public function omitBackground(): static
    {
        $this->formValue('omitBackground', true);

        return $this;
    }

    /**
     * Sets the duration (i.e., "1s", "2ms", etc.) to wait when loading the
     * page before converting it.
     */
    public function waitDelay(string $delay): static
    {
        $this->formValue('waitDelay', $delay);

        return $this;
    }

    /**
     * Sets the JavaScript expression to wait for before converting the page,
     * until it returns true.
     *
     * For instance: "window.status === 'ready'".
     */
    public function waitForExpression(string $expression): static
    {
        $this->formValue('waitForExpression', $expression);

        return $this;
    }

    /**
     * Sets the selector (e.g. '#id') to query before converting the page,
     * until it matches a node.
     */
    public function waitForSelector(string $selector): static
    {
        $this->formValue('waitForSelector', $selector);

        return $this;
    }

    /**
     * Waits until window.status equals the given value before converting the
     * page.
     */
    public function waitForWindowStatus(string $status): static
    {
        $this->formValue('waitForWindowStatus', $status);

        return $this;
    }

    /**
     * Forces Chromium to emulate the media type "print".
     */
    public function emulatePrintMediaType(): static
    {
        $this->formValue('emulatedMediaType', 'print');

        return $this;
    }

    /**
     * Forces Chromium to emulate the media type "screen".
     */
    public function emulateScreenMediaType(): static
    {
        $this->formValue('emulatedMediaType', 'screen');

        return $this;
    }

    /**
     * Overrides CSS media features, such as
     * ['prefers-color-scheme' => 'dark'].
     *
     * @param array<string,string> $features
     */
    public function emulatedMediaFeatures(array $features): static
    {
        $this->formValue('emulatedMediaFeatures', json_encode($features, JSON_THROW_ON_ERROR));

        return $this;
    }

    /**
     * Cookies to store in the Chromium cookie jar.
     */
    public function cookies(ChromiumCookie ...$cookies): static
    {
        $this->formValue('cookies', json_encode(array_values($cookies), JSON_THROW_ON_ERROR));

        return $this;
    }

    /**
     * Overrides the default 'User-Agent' HTTP header.
     */
    public function userAgent(string $userAgent): static
    {
        $this->formValue('userAgent', $userAgent);

        return $this;
    }

    /**
     * Sets extra HTTP headers that Chromium sends when loading the page.
     *
     * @param array<string,string> $headers
     */
    public function extraHttpHeaders(array $headers): static
    {
        $this->formValue('extraHttpHeaders', json_encode($headers, JSON_THROW_ON_ERROR));

        return $this;
    }

    /**
     * Forces Gotenberg to return a 409 Conflict response if the HTTP status
     * code from the main page is not acceptable.
     */
    public function failOnHttpStatusCodes(int ...$statusCodes): static
    {
        $this->formValue('failOnHttpStatusCodes', json_encode(array_values($statusCodes), JSON_THROW_ON_ERROR));

        return $this;
    }

    /**
     * Forces Gotenberg to return a 409 Conflict response if the HTTP status
     * code from at least one resource is not acceptable.
     */
    public function failOnResourceHttpStatusCodes(int ...$statusCodes): static
    {
        $this->formValue('failOnResourceHttpStatusCodes', json_encode(array_values($statusCodes), JSON_THROW_ON_ERROR));

        return $this;
    }

    /**
     * Excludes resources from failOnResourceHttpStatusCodes checks based on
     * their hostname.
     */
    public function ignoreResourceHttpStatusDomains(string ...$domains): static
    {
        $this->formValue('ignoreResourceHttpStatusDomains', json_encode(array_values($domains), JSON_THROW_ON_ERROR));

        return $this;
    }

    /**
     * Forces Gotenberg to return a 409 Conflict if Chromium fails to load at
     * least one resource.
     */
    public function failOnResourceLoadingFailed(): static
    {
        $this->formValue('failOnResourceLoadingFailed', true);

        return $this;
    }

    /**
     * Forces Gotenberg to return a 409 Conflict response if there are
     * exceptions in the Chromium console.
     */
    public function failOnConsoleExceptions(): static
    {
        $this->formValue('failOnConsoleExceptions', true);

        return $this;
    }

    /**
     * Forces Gotenberg to return a 409 Conflict response if the Chromium
     * console logs messages of the given types.
     */
    public function failOnConsoleMessageTypes(ConsoleMessageType ...$types): static
    {
        $this->formValue('failOnConsoleMessageTypes', json_encode(array_values($types), JSON_THROW_ON_ERROR));

        return $this;
    }

    /**
     * Specifies whether Chromium skips waiting for its network to be idle.
     * Pass false for pages that fetch content after they load.
     */
    public function skipNetworkIdleEvent(bool $skip = true): static
    {
        $this->formValue('skipNetworkIdleEvent', $skip);

        return $this;
    }

    /**
     * Specifies whether Chromium skips waiting for its network to be almost
     * idle.
     */
    public function skipNetworkAlmostIdleEvent(bool $skip = true): static
    {
        $this->formValue('skipNetworkAlmostIdleEvent', $skip);

        return $this;
    }

    /**
     * Sets the additional files, like images, fonts, stylesheets, and so on.
     */
    public function assets(Stream ...$assets): static
    {
        foreach ($assets as $asset) {
            $this->formFile($asset);
        }

        return $this;
    }

    /**
     * Converts a target URL.
     */
    public function url(string $url): RequestInterface
    {
        $this->formValue('url', $url);

        return $this->route('url');
    }

    /**
     * Converts an HTML document.
     *
     * Note: it automatically sets the index filename to "index.html", as
     * required by Gotenberg. Pass null when an archive or downloadFrom
     * provides it.
     */
    public function html(Stream|null $index): RequestInterface
    {
        if ($index !== null) {
            $this->formFile($index, filename: 'index.html');
        }

        return $this->route('html');
    }

    /**
     * Converts one or more markdown files.
     *
     * Note: it automatically sets the index filename to "index.html", as
     * required by Gotenberg.
     */
    public function markdown(Stream|null $index, Stream ...$markdowns): RequestInterface
    {
        if ($index !== null) {
            $this->formFile($index, filename: 'index.html');
        }

        foreach ($markdowns as $markdown) {
            $this->formFile($markdown);
        }

        return $this->route('markdown');
    }

    abstract private function route(string $source): RequestInterface;
}
