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
     * @var StepPrinter
     */
    private $stepPrinter;

    /**
     * @param ScenarioPrinter $scenarioPrinter
     * @param StepPrinter     $stepPrinter
     */
    public function __construct(ScenarioPrinter $scenarioPrinter, StepPrinter $stepPrinter)
    {
        $this->scenarioPrinter = $scenarioPrinter;
        $this->stepPrinter     = $stepPrinter;
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

        foreach ($outline->getSteps() as $step) {
            $this->stepPrinter->printStep($formatter, $outline, $step, new UndefinedStepResult());
        }

        $formatter->getOutputPrinter()->writeln();
        $formatter->getOutputPrinter()->writeln("|===");
        $formatter->getOutputPrinter()->writeln(
            sprintf('| %s', implode(' | ', $outline->getExampleTable()->getRow(0)))
        );
    }

    /**
     * @param Formatter  $formatter
     * @param TestResult $result
     */
    public function printFooter(Formatter $formatter, TestResult $result)
    {
        $formatter->getOutputPrinter()->writeln("|===");
        $formatter->getOutputPrinter()->writeln();
    }
}