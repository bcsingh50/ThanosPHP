<?php
declare(strict_types=1);

namespace ThanosPHP;

class InfinityGauntlet
{
    /** @var string[] Patterns to ignore */
    private array $ignorePatterns = [
        '#(^|/)\.git(/|$)#',           // ignore .git directories
        '#(^|/)(vendor|node_modules)(/|$)#', // ignore vendor and node_modules directories
        '#\.env$#',                     // ignore .env files
        '#composer\.json$#',            // ignore composer.json
    ];

    /**
     * Perform the snap.
     *
     * @param string $path The path to snap files from.
     * @param bool $dryRun If true, only show what would be deleted.
     * @param bool $withGauntlet Must be true to allow real deletion.
     */
    public function snap(string $path, bool $dryRun = false, bool $withGauntlet = false): void
    {
        if (!$dryRun && !$withGauntlet) {
            echo "Without the gauntlet I am nothing, run me with either --dry-run or if you are ready to face my wrath --with-gauntlet\n";
            return;
        }

        $files = $this->getFiles($path);

        if (empty($files)) {
            echo "No files found to snap.\n";
            return;
        }

        shuffle($files);
        $countToDelete = (int) floor(count($files) / 2);
        $filesToDelete = array_slice($files, 0, $countToDelete);

        foreach ($filesToDelete as $file) {
            if ($dryRun) {
                echo "[Dry Run] Would delete: $file\n";
            } else {
                if (is_file($file)) {
                    unlink($file);
                } elseif (is_dir($file)) {
                    $this->deleteDirectory($file);
                }
                echo "Deleted: $file\n";
            }
        }
    }

    private function getFiles(string $dir): array
    {
        $result = [];
        $items = scandir($dir);

        if ($items === false) {
            return [];
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $dir . DIRECTORY_SEPARATOR . $item;

            if ($this->shouldIgnore($fullPath)) {
                continue;
            }

            if (is_dir($fullPath)) {
                $result = array_merge($result, $this->getFiles($fullPath));
                $result[] = $fullPath; // add directory itself after contents
            } else {
                $result[] = $fullPath;
            }
        }

        return $result;
    }

    private function shouldIgnore(string $path): bool
    {
        $path = str_replace('\\', '/', $path); // normalize slashes

        foreach ($this->ignorePatterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    private function deleteDirectory(string $dir): void
    {
        $items = scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($fullPath)) {
                $this->deleteDirectory($fullPath);
            } else {
                unlink($fullPath);
            }
        }
        rmdir($dir);
    }
}
