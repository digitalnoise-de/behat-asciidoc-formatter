<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\ScenarioPrinter;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Result\TestResult;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocBackgroundPrinter implements ScenarioPrinter
{
    /**
     * @param Formatter   $formatter
     * @param FeatureNode $feature
     * @param Scenario    $scenario
     */
    public function printHeader(Formatter $formatter, FeatureNode $feature, Scenario $scenario)
    {
        $formatter->getOutputPrinter()->writeln(sprintf('.%s', $scenario->getKeyword()));
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
