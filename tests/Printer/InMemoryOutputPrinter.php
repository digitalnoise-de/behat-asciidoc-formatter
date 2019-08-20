<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Tests\Printer;

use Behat\Testwork\Output\Printer\OutputPrinter;

class InMemoryOutputPrinter implements OutputPrinter
{
    private $output = '';

    public function setOutputPath($path)
    {
    }

    public function getOutputPath()
    {
    }

    public function setOutputStyles(array $styles)
    {
    }

    public function getOutputStyles()
    {
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
