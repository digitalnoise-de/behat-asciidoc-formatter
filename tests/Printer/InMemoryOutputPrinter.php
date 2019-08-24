<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Testwork\Output\Printer\OutputPrinter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class InMemoryOutputPrinter implements OutputPrinter
{
    private $output = '';

    private $outputPath = '';

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

    public function write($messages)
    {
        $this->output .= $messages;
    }

    public function writeln($messages = '')
    {
        $this->output .= $messages . "\n";
    }

    public function flush()
    {
        $this->output = '';
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }
}
