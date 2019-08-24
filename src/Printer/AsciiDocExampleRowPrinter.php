<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\ExampleRowPrinter;
use Behat\Gherkin\Node\ExampleNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Testwork\Output\Formatter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocExampleRowPrinter implements ExampleRowPrinter
{
    /**
     * @param Formatter   $formatter
     * @param OutlineNode $outline
     * @param ExampleNode $example
     * @param array       $events
     */
    public function printExampleRow(Formatter $formatter, OutlineNode $outline, ExampleNode $example, array $events)
    {
        $formatter->getOutputPrinter()->writeln(sprintf('| %s', implode(' | ', $example->getTokens())));
    }
}
