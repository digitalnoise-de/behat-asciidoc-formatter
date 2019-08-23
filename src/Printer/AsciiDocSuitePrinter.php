<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\SuitePrinter;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Suite\Suite;

class AsciiDocSuitePrinter implements SuitePrinter
{
    public function printHeader(Formatter $formatter, Suite $suite)
    {
        $printer = $formatter->getOutputPrinter();

        $printer->writeln(sprintf('== %s', $suite->getName()));
        $printer->writeln();
    }

    public function printFooter(Formatter $formatter, Suite $suite)
    {
    }
}
