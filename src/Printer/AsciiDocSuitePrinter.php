<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\SuitePrinter;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Suite\Suite;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocSuitePrinter implements SuitePrinter
{
    public function printHeader(Formatter $formatter, Suite $suite)
    {
        $printer = $formatter->getOutputPrinter();

        $printer->writeln(sprintf('= %s', $suite->getName()));
        $printer->writeln();
    }

    public function printFooter(Formatter $formatter, Suite $suite)
    {
    }
}
