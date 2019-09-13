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
        $this->themeDirectory  = $themeDirectory;

        $this->filesystem = new Filesystem();
    }

    public function reset(string $outputDirectory): void
    {
        $iter = new FilesystemIterator(
            $outputDirectory,
            FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::SKIP_DOTS
        );

        foreach ($iter as $file) {
            $this->filesystem->remove($file);
        }

        $this->filesystem->mirror($this->themeDirectory, sprintf('%s/themes', $outputDirectory));
    }
}
