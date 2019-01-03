<?php

declare(strict_types=1);

namespace Babelfish\Internal;

class DataDumper
{
    /**
     * @var Dump[]
     */
    private $dumps;

    public function __construct(Dump ...$dumps)
    {
        $this->dumps = $dumps;
    }

    public function dump(string $linguist_repo_path): void
    {
        $commit_reference = $this->getCommitReference($linguist_repo_path);

        foreach ($this->dumps as $dump) {
            $generator = $dump->getGenerator();

            $exported_values = $generator->generate($linguist_repo_path);
            $this->save($exported_values, $dump->getOutputPath(), $commit_reference);
        }
    }

    private function save(array $exported_values, string $output_file, string $linguist_commit_reference): void
    {
        $marshalled_value = var_export($exported_values, true);

        $output_folder = dirname($output_file);
        if (! is_dir($output_folder) && ! mkdir($output_folder, 0777, true) && ! is_dir($output_folder)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $output_folder));
        }

        $res = file_put_contents(
            $output_file,
            <<<EOT
<?php

// Generated code, DO NOT EDIT
// Extracted from github/linguist $linguist_commit_reference

return $marshalled_value;
EOT
        );
        if ($res === false) {
            throw new FileNotWritableException($output_file);
        }
    }

    private function getCommitReference(string $linguist_repo_path): string
    {
        try {
            $head_file = new \SplFileObject($linguist_repo_path . '/.git/HEAD');
        } catch (\RuntimeException $ex) {
            return 'unknown';
        }
        $commit = $head_file->fgets();

        if (strpos($commit, 'ref: refs/heads/') !== 0) {
            return $commit;
        }

        try {
            $ref_file = new \SplFileObject($linguist_repo_path . '/.git/' . substr($commit, 5));
        } catch (\RuntimeException $ex) {
            return 'unknown';
        }

        return $ref_file->fgets() ?: 'unknown';
    }
}
