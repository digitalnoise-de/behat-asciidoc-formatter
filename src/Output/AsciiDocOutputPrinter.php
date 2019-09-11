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

    public function setFilename(string $filename): void
    {
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

    public function writeln($messages = '')
    {
        $this->stream->write($messages, true);
    }
}
