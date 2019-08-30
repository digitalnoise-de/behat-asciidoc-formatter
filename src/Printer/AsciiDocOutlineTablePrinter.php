<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\OutlineTablePrinter;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Result\TestResult;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocOutlineTablePrinter implements OutlineTablePrinter
{
    /**
     * @param Formatter    $formatter
     * @param FeatureNode  $feature
     * @param OutlineNode  $outline
     * @param StepResult[] $results
     */
    public function printHeader(Formatter $formatter, FeatureNode $feature, OutlineNode $outline, array $results)
    {
        $outputPrinter = $formatter->getOutputPrinter();

        $outputPrinter->writeln();
        $outputPrinter->writeln("|===");
        $outputPrinter->writeln(sprintf('| %s', implode(' | ', $outline->getExampleTable()->getRow(0))));
    }

    /**
     * @param Formatter  $formatter
     * @param TestResult $result
     */
    public function printFooter(Formatter $formatter, TestResult $result)
    {
        $formatter->getOutputPrinter()->writeln("|===");
    }
}
