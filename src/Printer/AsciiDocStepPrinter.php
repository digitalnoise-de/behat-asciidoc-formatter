<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\StepPrinter;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Gherkin\Node\StepNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Behat\Testwork\Tester\Result\ExceptionResult;

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
        $this->printStdOut($printer, $result);
        $this->printException($printer, $result);
        $this->printBlockEnd($printer, $result);
    }

    /**
     * @param OutputPrinter $printer
     * @param StepResult    $stepResult
     */
    private function printBlockStart(OutputPrinter $printer, StepResult $stepResult): void
    {
        if ($stepResult->getResultCode() === StepResult::FAILED) {
            $printer->writeln('[WARNING]');
            $printer->writeln('====');
        }

        if ($stepResult->getResultCode() === StepResult::PENDING) {
            $printer->writeln('[CAUTION]');
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
        $printer->write($this->formatText(sprintf('*%s* %s', $step->getKeyword(), $step->getText()), $result));
        $printer->writeln(' +');
    }

    /**
     * @param StepResult $result
     *
     * @return string
     */
    private function formatText(string $text, StepResult $result): string
    {
        $role = $this->getRole($result);
        if ($role === null) {
            return $text;
        }

        return sprintf('[%s]#%s#', $role, $text);
    }

    /**
     * @param StepResult $result
     *
     * @return string|null
     */
    private function getRole(StepResult $result): ?string
    {
        switch ($result->getResultCode()) {
            case StepResult::FAILED:
                return 'red';

            case StepResult::PENDING:
            case StepResult::UNDEFINED:
                return 'yellow';

            case StepResult::SKIPPED:
                return 'blue';

            default:
                return null;
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
    private function printStdOut(OutputPrinter $printer, StepResult $stepResult): void
    {
        if ($stepResult instanceof ExecutedStepResult && $stepResult->getCallResult()->hasStdOut()) {
            $printer->writeln('----');
            $printer->writeln($stepResult->getCallResult()->getStdOut());
            $printer->writeln('----');
        }
    }

    /**
     * @param OutputPrinter $printer
     * @param StepResult    $stepResult
     */
    private function printException(OutputPrinter $printer, StepResult $stepResult)
    {
        if ($stepResult instanceof ExceptionResult && $stepResult->hasException()) {
            $printer->writeln('----');
            $printer->writeln($stepResult->getException()->getMessage());
            $printer->writeln('----');
        }
    }

    /**
     * @param OutputPrinter $printer
     * @param StepResult    $stepResult
     */
    private function printBlockEnd(OutputPrinter $printer, StepResult $stepResult): void
    {
        if (in_array($stepResult->getResultCode(), [StepResult::PENDING, StepResult::FAILED])) {
            $printer->writeln('====');
        }
    }
}
