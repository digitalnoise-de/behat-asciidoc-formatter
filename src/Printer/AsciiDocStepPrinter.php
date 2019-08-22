<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\StepPrinter;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Gherkin\Node\StepNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;

/**
 * Class AsciiDocStepPrinter
 *
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocStepPrinter implements StepPrinter
{
    /**
     * @param Formatter  $formatter
     * @param Scenario   $scenario
     * @param StepNode   $step
     * @param StepResult $result
     */
    public function printStep(Formatter $formatter, Scenario $scenario, StepNode $step, StepResult $result)
    {
        $printer = $formatter->getOutputPrinter();

        $this->printBlockStart($printer, $result);
        $this->printText($printer, $step, $result);
        $this->printArguments($printer, $step);
        $this->printBlockEnd($printer, $result);
    }

    /**
     * @param OutputPrinter $printer
     * @param StepResult    $stepResult
     */
    public function printBlockStart(OutputPrinter $printer, StepResult $stepResult): void
    {
        if ($stepResult->getResultCode() === StepResult::FAILED) {
            $printer->writeln('[WARNING]');
            $printer->writeln('====');
        }
    }

    /**
     * @param OutputPrinter $printer
     * @param StepNode      $step
     * @param StepResult    $result
     */
    private function printText(OutputPrinter $printer, StepNode $step, StepResult $result): void
    {
        $printer->writeln(sprintf($this->getTemplateForResult($result), $step->getKeyword(), $step->getText()));
    }

    /**
     * @param StepResult $result
     *
     * @return string
     */
    private function getTemplateForResult(StepResult $result): string
    {
        switch ($result->getResultCode()) {
            case StepResult::FAILED:
                return '[red]#*%s* %s# +';

            case StepResult::PENDING:
            case StepResult::UNDEFINED:
                return '[yellow]#*%s* %s# +';

            case StepResult::SKIPPED:
                return '[blue]#*%s* %s# +';

            default:
                return '*%s* %s +';
        }
    }

    /**
     * @param OutputPrinter $printer
     * @param StepNode      $step
     */
    public function printArguments(OutputPrinter $printer, StepNode $step): void
    {
        foreach ($step->getArguments() as $argument) {
            if ($argument instanceof TableNode) {
                $this->printTable($printer, $argument);
            }
        }
    }

    /**
     * @param OutputPrinter $printer
     * @param TableNode     $tableNode
     */
    private function printTable(OutputPrinter $printer, TableNode $tableNode): void
    {
        $printer->writeln('[stripes=even,options="header"]');
        $printer->writeln('|===');

        foreach ($tableNode->getRows() as $row) {
            $printer->writeln(sprintf('| %s', implode(' | ', $row)));
        }

        $printer->writeln('|===');
    }

    /**
     * @param OutputPrinter $printer
     * @param StepResult    $stepResult
     */
    public function printBlockEnd(OutputPrinter $printer, StepResult $stepResult): void
    {
        if ($stepResult->getResultCode() === StepResult::FAILED) {
            $printer->writeln('====');
        }
    }
}
