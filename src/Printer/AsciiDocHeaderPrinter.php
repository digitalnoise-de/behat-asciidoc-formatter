<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Testwork\Output\Formatter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocHeaderPrinter
{
    /**
     * @var string
     */
    private $title;

    /**
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param Formatter $formatter
     */
    public function printHeader(Formatter $formatter): void
    {
        $printer = $formatter->getOutputPrinter();

        $printer->writeln(sprintf('= %s', $this->title));
        $printer->writeln(':doctype: book');
        $printer->writeln(':icons: font');
        $printer->writeln(':toc:');
        $printer->writeln(':toclevels: 3');
        $printer->writeln(':pdf-theme: behat');
        $printer->writeln(':pdf-themesdir: themes');
        $printer->writeln();
    }
}
