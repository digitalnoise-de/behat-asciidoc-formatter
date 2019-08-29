<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\OutlineTablePrinter;
use Behat\Behat\Output\Node\Printer\ScenarioPrinter;
use Behat\Behat\Output\Node\Printer\StepPrinter;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Behat\Tester\Result\UndefinedStepResult;
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
     * @var ScenarioPrinter
     */
    private $scenarioPrinter;

    /**
     * @var AsciiDocBackgroundPrinter
     */
    private $backgroundPrinter;

    /**
     * @var StepPrinter
     */
    private $stepPrinter;

    /**
     * @param ScenarioPrinter           $scenarioPrinter
     * @param AsciiDocBackgroundPrinter $backgroundPrinter
     * @param StepPrinter               $stepPrinter
     */
    public function __construct(
        ScenarioPrinter $scenarioPrinter,
        AsciiDocBackgroundPrinter $backgroundPrinter,
        StepPrinter $stepPrinter
    ) {
        $this->scenarioPrinter   = $scenarioPrinter;
        $this->backgroundPrinter = $backgroundPrinter;
        $this->stepPrinter       = $stepPrinter;
    }

    /**
     * @param Formatter    $formatter
     * @param FeatureNode  $feature
     * @param OutlineNode  $outline
     * @param StepResult[] $results
     */
    public function printHeader(Formatter $formatter, FeatureNode $feature, OutlineNode $outline, array $results)
    {
        $this->scenarioPrinter->printHeader($formatter, $feature, $outline);

        $this->printBackground($formatter, $feature);
        $this->printSteps($formatter, $outline);
        $this->printTableHeader($formatter, $outline);
    }

    /**
     * @param Formatter   $formatter
     * @param FeatureNode $feature
     */
    private function printBackground(Formatter $formatter, FeatureNode $feature): void
    {
        if ($feature->getBackground() === null) {
            return;
        }

        $this->backgroundPrinter->printBackground($formatter, $feature->getBackground());
    }

    /**
     * @param Formatter   $formatter
     * @param OutlineNode $outline
     */
    private function printSteps(Formatter $formatter, OutlineNode $outline): void
    {
        foreach ($outline->getSteps() as $step) {
            $this->stepPrinter->printStep($formatter, $outline, $step, new UndefinedStepResult());
        }
    }

    /**
     * @param Formatter   $formatter
     * @param OutlineNode $outline
     */
    private function printTableHeader(Formatter $formatter, OutlineNode $outline): void
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
        $this->printTableFooter($formatter);
    }

    /**
     * @param Formatter $formatter
     */
    private function printTableFooter(Formatter $formatter): void
    {
        $outputPrinter = $formatter->getOutputPrinter();

        $outputPrinter->writeln("|===");
        $outputPrinter->writeln();
    }
}
