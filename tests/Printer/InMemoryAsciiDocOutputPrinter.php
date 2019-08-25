<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Digitalnoise\Behat\AsciiDocFormatter\Output\AsciiDocOutputPrinter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class InMemoryAsciiDocOutputPrinter extends AsciiDocOutputPrinter
{
    /**
     * @var string
     */
    private $currentFilename;

    /**
     * @var string[]
     */
    private $output = [];

    /**
     * @var string
     */
    private $outputPath = '';

    public function __construct()
    {
    }

    public function setFilename(string $filename): void
    {
        $this->currentFilename = $filename;
        $this->flush();
    }

    public function flush()
    {
        $this->output[$this->currentFilename] = '';
    }

    public function getOutputPath()
    {
        return $this->outputPath;
    }

    public function setOutputPath($path)
    {
        $this->outputPath = $path;
    }

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
    }

    public function setOutputVerbosity($level)
    {
    }

    public function getOutputVerbosity()
    {
    }

    public function writeln($messages = '')
    {
        $this->write($messages . "\n");
    }

    public function write($messages)
    {
        $this->output[$this->currentFilename] .= $messages;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output[$this->currentFilename];
    }

    /**
     * @return string
     */
    public function getCurrentFilename(): string
    {
        return $this->currentFilename;
    }
}
