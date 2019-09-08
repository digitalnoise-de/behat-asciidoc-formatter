<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\ExampleNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Result\TestResults;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocOutlinePrinter
{
    private const ICON_COLUMN_PERCENTAGE = 4;

    /**
     * @var AsciiDocScenarioPrinter
     */
    private $scenarioPrinter;

    /**
     * @var ResultFormatter
     */
    private $resultFormatter;

    /**
     * @param AsciiDocScenarioPrinter $scenarioPrinter
     * @param ResultFormatter         $resultFormatter
     */
    public function __construct(AsciiDocScenarioPrinter $scenarioPrinter, ResultFormatter $resultFormatter)
    {
        $this->scenarioPrinter = $scenarioPrinter;
        $this->resultFormatter = $resultFormatter;
    }

    /**
     * @param Formatter   $formatter
     * @param FeatureNode $feature
     * @param OutlineNode $outline
     * @param TestResult  $result
     */
    public function printHeader(Formatter $formatter, FeatureNode $feature, OutlineNode $outline, TestResult $result)
    {
        $outputPrinter = $formatter->getOutputPrinter();

        $this->scenarioPrinter->printScenario($formatter, $feature, $outline, [], $result);

        $header = $outline->getExampleTable()->getRow(0);
        $cols   = sprintf('%s%%%s', self::ICON_COLUMN_PERCENTAGE, str_repeat(',~', count($header)));

        $outputPrinter->writeln();
        $outputPrinter->writeln(sprintf('[cols="%s", options="header", caption=]', $cols));
        $outputPrinter->writeln(sprintf('.%s', $outline->getExampleTable()->getKeyword()));
        $outputPrinter->writeln("|===");
        $outputPrinter->writeln(sprintf('| | %s', implode(' | ', $outline->getExampleTable()->getRow(0))));
    }

    /**
     * @param Formatter    $formatter
     * @param OutlineNode  $outline
     * @param ExampleNode  $example
     * @param StepResult[] $stepResults
     */
    public function printExample(Formatter $formatter, OutlineNode $outline, ExampleNode $example, array $stepResults)
    {
        $outputPrinter = $formatter->getOutputPrinter();

        list($stdOut, $exceptions) = $this->collectCallResultsOutput($stepResults);
        $hasOutput = count($stdOut) > 0 || count($exceptions) > 0;
        if ($hasOutput) {
            $outputPrinter->write('.2+');
        }

        $outputPrinter->writeln(sprintf('| %s', $this->getIconForResult(new TestResults($stepResults))));

        $tokens = $example->getTokens();
        foreach (array_values($tokens) as $index => $token) {
            $column = $outline->getExampleTable()->getRow(0)[$index];

            $matchingResults = [];
            foreach ($outline->getSteps() as $step) {
                if (strpos($step->getText(), sprintf('<%s>', $column)) !== false) {
                    $matchingResults[] = $stepResults[$step->getLine()];
                }
            }

            $result = new TestResults($matchingResults);

            $outputPrinter->writeln(sprintf('| %s', $this->resultFormatter->format($token, 'Step', $result)));
        }

        $this->printOutputRow($outputPrinter, count($tokens), $stdOut, $exceptions);
    }

    /**
     * @param StepResult[] $stepResults
     *
     * @return array
     */
    private function collectCallResultsOutput(array $stepResults): array
    {
        $stdOut     = [];
        $exceptions = [];

        foreach ($stepResults as $stepResult) {
            if ($stepResult instanceof ExecutedStepResult) {
                if ($stepResult->getCallResult()->hasStdOut()) {
                    $stdOut[] = $stepResult->getCallResult()->getStdOut();
                }

                if ($stepResult->hasException()) {
                    $exceptions[] = $stepResult->getException()->getMessage();
                }
            }
        }

        return [$stdOut, $exceptions];
    }

    /**
     * @param TestResult $testResult
     *
     * @return string
     */
    private function getIconForResult(TestResult $testResult): string
    {
        if ($testResult->isPassed()) {
            return 'icon:check-circle[]';
        }

        return 'icon:exclamation-circle[]';
    }

    /**
     * @param OutputPrinter $outputPrinter
     * @param int           $cols
     * @param array         $stdOut
     * @param array         $exceptions
     */
    public function printOutputRow(OutputPrinter $outputPrinter, int $cols, array $stdOut, array $exceptions): void
    {
        if (count($stdOut) === 0 && count($exceptions) === 0) {
            return;
        }

        $outputPrinter->write(sprintf('%d+| ', $cols));

        if (count($stdOut) > 0) {
            $outputPrinter->writeln(implode("\n", $stdOut));
        }

        if (count($exceptions) > 0) {
            $outputPrinter->writeln(implode("\n", $exceptions));
        }
    }

    /**
     * @param Formatter $formatter
     */
    public function printFooter(Formatter $formatter)
    {
        $formatter->getOutputPrinter()->writeln("|===");
    }
}
