<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use SplFileObject;
use function file_exists;

trait GetContentFromLinguistFileTrait
{
    private function getContent(string $linguist_repo_path, string $linguist_file) : string
    {
        $file_to_read = $linguist_repo_path . '/' . $linguist_file;
        if (! file_exists($file_to_read)) {
            throw new FileDoesNotExist($file_to_read);
        }
        $file = new SplFileObject($file_to_read);

        return $file->fread($file->getSize());
    }
}
