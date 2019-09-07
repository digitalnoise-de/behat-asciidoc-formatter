<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\StepPrinter;
use Behat\Behat\Tester\Result\UndefinedStepResult;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Result\TestResult;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocScenarioPrinter
{
    /**
     * @var StepPrinter
     */
    private $stepPrinter;

    /**
     * @var ResultFormatter
     */
    private $resultFormatter;

    /**
     * @param StepPrinter     $stepPrinter
     * @param ResultFormatter $resultFormatter
     */
    public function __construct(StepPrinter $stepPrinter, ResultFormatter $resultFormatter)
    {
        $this->stepPrinter     = $stepPrinter;
        $this->resultFormatter = $resultFormatter;
    }

    /**
     * @param Formatter         $formatter
     * @param FeatureNode       $feature
     * @param ScenarioInterface $scenario
     * @param TestResult[]      $stepResults
     * @param TestResult        $result
     */
    public function printScenario(
        Formatter $formatter,
        FeatureNode $feature,
        ScenarioInterface $scenario,
        array $stepResults,
        TestResult $result
    ) {
        $this->printTitle($formatter, $feature, $scenario, $result);
        $this->printBackground($formatter, $feature, $stepResults);
        $this->printSteps($formatter, $scenario, $stepResults);
    }

    /**
     * @param Formatter         $formatter
     * @param FeatureNode       $feature
     * @param ScenarioInterface $scenario
     * @param TestResult        $result
     */
    private function printTitle(
        Formatter $formatter,
        FeatureNode $feature,
        ScenarioInterface $scenario,
        TestResult $result
    ): void {
        $title = $scenario->getTitle();
        if (empty($title)) {
            $title = sprintf('%s:%s', $feature->getFile(), $scenario->getLine());
        }

        $formatter->getOutputPrinter()->writeln(
            sprintf('==== %s', $this->resultFormatter->format($title, $scenario->getNodeType(), $result))
        );
        $formatter->getOutputPrinter()->writeln();
    }

    /**
     * @param Formatter   $formatter
     * @param FeatureNode $feature
     * @param array       $stepResults
     */
    private function printBackground(Formatter $formatter, FeatureNode $feature, array $stepResults): void
    {
        $background = $feature->getBackground();
        if ($background === null) {
            return;
        }

        $formatter->getOutputPrinter()->writeln(sprintf('.%s', $background->getKeyword()));
        $this->printSteps($formatter, $background, $stepResults);
    }

    /**
     * @param Formatter             $formatter
     * @param ScenarioLikeInterface $scenario
     * @param array                 $stepResults
     */
    private function printSteps(Formatter $formatter, ScenarioLikeInterface $scenario, array $stepResults): void
    {
        if (count($scenario->getSteps()) === 0) {
            return;
        }

        foreach ($scenario->getSteps() as $step) {
            $result = $stepResults[$step->getLine()] ?? new UndefinedStepResult();

            $this->stepPrinter->printStep($formatter, $scenario, $step, $result);
        }

        $formatter->getOutputPrinter()->writeln();
    }
}
