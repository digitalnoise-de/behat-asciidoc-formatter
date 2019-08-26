<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Output\AsciiDocOutputPrinter;
use RuntimeException;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocHeaderPrinter
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string $filename
     * @param string $title
     */
    public function __construct(string $filename, string $title)
    {
        $this->filename = $filename;
        $this->title    = $title;
    }

    /**
     * @param Formatter $formatter
     */
    public function printHeader(Formatter $formatter): void
    {
        $printer = $formatter->getOutputPrinter();

        $this->setFilename($printer);
        $printer->writeln(sprintf('= %s', $this->title));
        $printer->writeln(':icons: font');
        $printer->writeln(':toc:');
        $printer->writeln();
    }

    /**
     * @param OutputPrinter $printer
     */
    private function setFilename(OutputPrinter $printer): void
    {
        if (!$printer instanceof AsciiDocOutputPrinter) {
            throw new RuntimeException(
                sprintf('Expected "%s", got "%s"', AsciiDocOutputPrinter::class, get_class($printer))
            );
        }

        $printer->setFilename($this->filename);
    }
}
