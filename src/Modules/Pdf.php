<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\MultipartFormDataModule;
use Gotenberg\Stage;
use Gotenberg\StagesModule;
use Gotenberg\Stream;
use Psr\Http\Message\RequestInterface;

use function is_array;
use function json_encode;

use const JSON_THROW_ON_ERROR;

class Pdf
{
    use MultipartFormDataModule;
    use StagesModule;

    /**
     * Sets the password that opens encrypted PDFs, for the routes that read.
     * The other routes take a decrypt stage instead.
     */
    public function password(string $password): static
    {
        $this->formValue('password', $password);

        return $this;
    }

    /**
     * Runs the stages set with stages() on the PDFs, in order.
     * Gotenberg returns the PDF, or a ZIP archive when several PDFs come out.
     */
    public function pipe(Stream ...$pdfs): RequestInterface
    {
        $this->inputFiles(...$pdfs);
        $this->endpoint = '/forms/pdf/pipe';

        return $this->request();
    }

    /**
     * Runs one stage on the PDFs, through its own route, /forms/pdf/{op}.
     * Gotenberg returns the PDF, or a ZIP archive when several PDFs come out.
     */
    public function run(Stage $stage, Stream ...$pdfs): RequestInterface
    {
        foreach ($stage->formFields() as $name => $value) {
            $this->formValue($name, $value);
        }

        $this->stageFiles($stage);
        $this->inputFiles(...$pdfs);
        $this->endpoint = '/forms/pdf/' . $stage->op;

        return $this->request();
    }

    /**
     * Reads the page count, the PDF version, and whether the PDFs are
     * encrypted, tagged, linearized or hold form fields. Gotenberg returns
     * JSON, one entry per PDF under its file name.
     */
    public function info(Stream ...$pdfs): RequestInterface
    {
        return $this->read('info', ...$pdfs);
    }

    /**
     * Reads the metadata of the PDFs. Gotenberg returns JSON, one entry per
     * PDF under its file name.
     */
    public function metadata(Stream ...$pdfs): RequestInterface
    {
        return $this->read('metadata', ...$pdfs);
    }

    /**
     * Reads the bookmarks of the PDFs. Gotenberg returns JSON, one entry per
     * PDF under its file name.
     */
    public function bookmarks(Stream ...$pdfs): RequestInterface
    {
        return $this->read('bookmarks', ...$pdfs);
    }

    /**
     * Lists the files embedded in the PDFs. Gotenberg returns JSON, one entry
     * per PDF under its file name.
     */
    public function attachments(Stream ...$pdfs): RequestInterface
    {
        return $this->read('attachments', ...$pdfs);
    }

    /**
     * Lists the form fields of the PDFs, optionally on the given pages only.
     * Gotenberg returns JSON, one entry per PDF under its file name.
     */
    public function form(string|null $pages, Stream ...$pdfs): RequestInterface
    {
        if ($pages !== null) {
            $this->formValue('pages', $pages);
        }

        return $this->read('form', ...$pdfs);
    }

    /**
     * Checks the PDFs against a profile, such as "PDF/A-3b", "PDF/UA-1" or
     * "Factur-X", or against a PDF/A profile and a PDF/UA one together.
     * Gotenberg returns JSON, one entry per PDF under its file name.
     *
     * @param string|list<string> $profile
     */
    public function validate(string|array $profile, Stream ...$pdfs): RequestInterface
    {
        $this->formValue('profile', is_array($profile) ? json_encode($profile, JSON_THROW_ON_ERROR) : $profile);

        return $this->read('validate', ...$pdfs);
    }

    private function read(string $route, Stream ...$pdfs): RequestInterface
    {
        foreach ($pdfs as $pdf) {
            $this->formFile($pdf);
        }

        $this->endpoint = '/forms/pdf/' . $route;

        return $this->request();
    }
}
