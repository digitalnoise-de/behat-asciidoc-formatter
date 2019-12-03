<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Output;

use Behat\Testwork\Output\Exception\BadOutputPathException;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocOutputPrinter implements OutputPrinter
{
    /**
     * @var string
     */
    private $outputPath;

    /**
     * @var StreamOutput
     */
    private $stream;

    /**
     * @var string
     */
    private $filename;

    public function setOutputStyles(array $styles)
    {
    }

    public function getOutputStyles()
    {
        return [];
    }

    public function setOutputDecorated($decorated)
    {
    }

    public function isOutputDecorated()
    {
        return false;
    }

    public function setOutputVerbosity($level)
    {
    }

    public function getOutputVerbosity()
    {
    }

    public function write($messages)
    {
        $this->stream->write($messages);
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;

        if (is_file($this->getOutputPath())) {
            throw new BadOutputPathException(
                'Directory expected for the `output_path` option, but a filename was given.',
                $this->getOutputPath()
            );
        } elseif (!is_dir($this->getOutputPath())) {
            mkdir($this->getOutputPath(), 0777, true);
        }

        $filePath = sprintf('%s/%s', $this->outputPath, $filename);
        $path     = dirname($filePath);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $this->stream = new StreamOutput(fopen($filePath, 'a'), StreamOutput::VERBOSITY_NORMAL, false);
    }

    public function getOutputPath()
    {
        return $this->outputPath;
    }

    public function setOutputPath($path)
    {
        $this->outputPath = $path;
    }

    public function flush()
    {
    }

    public function pageBreak(): void
    {
        $this->writeln();
        $this->writeln('<<<');
        $this->writeln();
    }

    /**
     * @param string $messages
     */
    public function writeln($messages = '')
    {
        $this->stream->write($messages, true);
    }

    /**
     * @param string $filenames,...
     */
    public function include(string ...$filenames): void
    {
        foreach ($filenames as $filename) {
            $this->writeln(sprintf('include::%s[leveloffset=+1]', $this->relativePath($filename)));
        }
    }

    /**
     * @param $filename
     *
     * @return string
     */
    private function relativePath($filename): string
    {
        $dir  = explode(DIRECTORY_SEPARATOR, dirname(sprintf('%s/%s', $this->outputPath, $this->filename)));
        $file = explode(DIRECTORY_SEPARATOR, sprintf('%s/%s', $this->outputPath, $filename));

        while ($dir && $file && ($dir[0] == $file[0])) {
            array_shift($dir);
            array_shift($file);
        }

        return str_repeat('..'.DIRECTORY_SEPARATOR, count($dir)).implode(DIRECTORY_SEPARATOR, $file);
    }
}
