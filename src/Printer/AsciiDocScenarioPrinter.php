<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\ScenarioPrinter;
use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Behat\Testwork\Tester\Result\TestResult;

/**
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

        $this->printTitle($printer, $feature, $scenario);
    }

    /**
     * @param OutputPrinter $printer
     * @param FeatureNode   $feature
     * @param Scenario      $scenario
     */
    public function printTitle(OutputPrinter $printer, FeatureNode $feature, Scenario $scenario): void
    {
        if ($scenario instanceof BackgroundNode) {
            $printer->writeln(sprintf('.%s', $scenario->getKeyword()));
            return;
        }

        $title = $scenario->getTitle();
        if (empty($title)) {
            $title = sprintf('%s:%s', $feature->getFile(), $scenario->getLine());
        }

        $printer->writeln(sprintf('==== %s', $title));
        $printer->writeln();
    }

    /**
     * @param Formatter  $formatter
     * @param TestResult $result
     */
    public function printFooter(Formatter $formatter, TestResult $result)
    {
        $formatter->getOutputPrinter()->writeln();
        $formatter->getOutputPrinter()->writeln('<<<');
        $formatter->getOutputPrinter()->writeln();
    }
}
