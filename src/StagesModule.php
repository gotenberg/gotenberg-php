<?php

declare(strict_types=1);

namespace Gotenberg;

use function array_values;
use function json_encode;

use const JSON_THROW_ON_ERROR;

/**
 * Runs stages on the PDFs a route makes or receives. Requires
 * MultipartFormDataModule.
 */
trait StagesModule
{
    private Index|null $index = null;
    private bool $merge       = false;

    /**
     * Overrides the default index generator that keeps the files in the order
     * you pass them when a merge stage runs. Gotenberg merges the files in the
     * alphanumeric order of their names.
     */
    public function index(Index $index): static
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Sets the stages Gotenberg runs, in order, and uploads the files they
     * read.
     */
    public function stages(Stage ...$stages): static
    {
        $this->formValue('stages', json_encode(array_values($stages), JSON_THROW_ON_ERROR));

        foreach ($stages as $stage) {
            $this->stageFiles($stage);
        }

        return $this;
    }

    /**
     * Uploads the files a stage reads, and records whether it merges.
     */
    private function stageFiles(Stage $stage): void
    {
        if ($stage->op === 'merge') {
            $this->merge = true;
        }

        foreach ($stage->files() as $file) {
            $this->formFile($file);
        }
    }

    /**
     * Uploads the input files. With a merge stage, each name gets an index
     * prefix, so that Gotenberg merges them in the order you pass them.
     */
    private function inputFiles(Stream ...$files): void
    {
        $index = $this->index ?? new HrtimeIndex();

        foreach ($files as $file) {
            $this->formFile($file, filename: $this->merge ? $index->create() . '_' . $file->filename : null);
        }
    }

    abstract protected function formValue(string $name, string|int|float|bool $value): void;

    abstract protected function formFile(Stream $file, string $name = 'files', string|null $filename = null): void;
}
