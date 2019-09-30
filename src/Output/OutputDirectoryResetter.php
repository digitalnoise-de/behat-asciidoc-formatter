<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Output;

use FilesystemIterator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class OutputDirectoryResetter
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $themeDirectory;

    /**
     * @param string $themeDirectory
     */
    public function __construct(string $themeDirectory)
    {
        $this->themeDirectory = $themeDirectory;

        $this->filesystem = new Filesystem();
    }

    public function reset(string $outputDirectory): void
    {
        $this->ensureDirectoryExists($outputDirectory);
        $this->clearDirectory($outputDirectory);
        $this->copyThemes($outputDirectory);
    }

    /**
     * @param string $outputDirectory
     */
    public function ensureDirectoryExists(string $outputDirectory): void
    {
        if (!$this->filesystem->exists($outputDirectory)) {
            $this->filesystem->mkdir($outputDirectory);
        }
    }

    /**
     * @param string $outputDirectory
     */
    public function clearDirectory(string $outputDirectory): void
    {
        $iter = new FilesystemIterator(
            $outputDirectory,
            FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::SKIP_DOTS
        );

        foreach ($iter as $file) {
            $this->filesystem->remove($file);
        }
    }

    /**
     * @param string $outputDirectory
     */
    public function copyThemes(string $outputDirectory): void
    {
        $this->filesystem->mirror($this->themeDirectory, sprintf('%s/themes', $outputDirectory));
    }
}
