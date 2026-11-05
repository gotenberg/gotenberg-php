<?php

declare(strict_types=1);

namespace Gotenberg;

use GuzzleHttp\Psr7\MultipartStream;
use Psr\Http\Message\RequestInterface;

use function is_bool;
use function json_encode;

use const JSON_THROW_ON_ERROR;

trait MultipartFormDataModule
{
    use ApiModule {
        request as private apiRequest;
    }

    /** @var list<array{name: string, contents: mixed, filename?: string}> */
    private array $multipartFormData = [];

    /**
     * Overrides the default UUID output filename.
     * Note: Gotenberg adds the file extension automatically; you don't have to
     * set it.
     */
    public function outputFilename(string $filename): static
    {
        $this->headers['Gotenberg-Output-Filename'] = $filename;

        return $this;
    }

    /**
     * Sets the URLs Gotenberg downloads files from, as if you had uploaded
     * them.
     */
    public function downloadFrom(DownloadFrom ...$downloadFrom): static
    {
        $this->formValue('downloadFrom', json_encode($downloadFrom, JSON_THROW_ON_ERROR));

        return $this;
    }

    /**
     * Adds ZIP archives that Gotenberg unpacks: each entry counts as an
     * uploaded file.
     */
    public function archives(Stream ...$archives): static
    {
        foreach ($archives as $archive) {
            $this->formFile($archive, 'archives');
        }

        return $this;
    }

    /**
     * Uploads the result to $url instead of returning it, then sends a
     * webhook.success or webhook.error event to $eventsUrl.
     */
    public function webhook(string $url, string $eventsUrl): static
    {
        $this->headers['Gotenberg-Webhook-Url']        = $url;
        $this->headers['Gotenberg-Webhook-Events-Url'] = $eventsUrl;

        return $this;
    }

    /**
     * Overrides the default HTTP method that Gotenberg uses to upload the
     * result to the webhook.
     *
     * Either "POST", "PATCH", or "PUT" - default "POST".
     */
    public function webhookMethod(string $method): static
    {
        $this->headers['Gotenberg-Webhook-Method'] = $method;

        return $this;
    }

    /**
     * Sets the extra HTTP headers that Gotenberg sends alongside the upload
     * and the event.
     *
     * @param array<string,string> $headers
     */
    public function webhookExtraHttpHeaders(array $headers): static
    {
        $this->headers['Gotenberg-Webhook-Extra-Http-Headers'] = json_encode($headers, JSON_THROW_ON_ERROR);

        return $this;
    }

    protected function formValue(string $name, string|int|float|bool $value): void
    {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        $this->multipartFormData[] = [
            'name' => $name,
            'contents' => (string) $value,
        ];
    }

    protected function formFile(Stream $file, string $name = 'files', string|null $filename = null): void
    {
        $this->multipartFormData[] = [
            'name' => $name,
            'filename' => $filename ?? $file->filename,
            'contents' => $file->stream,
        ];
    }

    protected function request(string $method = 'POST'): RequestInterface
    {
        $body = new MultipartStream($this->multipartFormData);

        return $this->apiRequest($method)
            ->withHeader('Content-Type', 'multipart/form-data; boundary="' . $body->getBoundary() . '"')
            ->withBody($body);
    }
}
