<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\ScenarioPrinter;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Result\TestResult;

/**
 * Class AsciiDocScenarioPrinter
 *
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocScenarioPrinter implements ScenarioPrinter
{
    /**
     * @param Formatter   $formatter
     * @param FeatureNode $feature
     * @param Scenario    $scenario
     */
    public function printHeader(Formatter $formatter, FeatureNode $feature, Scenario $scenario)
    {
        $printer = $formatter->getOutputPrinter();
        $printer->writeln(sprintf('=== %s', $scenario->getTitle()));
        $printer->writeln();
    }

    /**
     * @param Formatter  $formatter
     * @param TestResult $result
     */
    public function printFooter(Formatter $formatter, TestResult $result)
    {
        $formatter->getOutputPrinter()->writeln();
    }
}
