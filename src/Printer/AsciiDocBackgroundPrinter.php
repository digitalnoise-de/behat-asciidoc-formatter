<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\StepPrinter;
use Behat\Behat\Tester\Result\UndefinedStepResult;
use Behat\Gherkin\Node\BackgroundNode;
use Behat\Testwork\Output\Formatter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocBackgroundPrinter
{
    /**
     * @var StepPrinter
     */
    private $stepPrinter;

    /**
     * @param StepPrinter $stepPrinter
     */
    public function __construct(StepPrinter $stepPrinter)
    {
        $this->stepPrinter = $stepPrinter;
    }

    /**
     * @param Formatter      $formatter
     * @param BackgroundNode $background
     */
    public function printBackground(Formatter $formatter, BackgroundNode $background): void
    {
        $printer = $formatter->getOutputPrinter();

        $printer->writeln(sprintf('.%s', $background->getKeyword()));
        foreach ($background->getSteps() as $step) {
            $this->stepPrinter->printStep($formatter, $background, $step, new UndefinedStepResult());
        }
        $printer->writeln();
    }
}
