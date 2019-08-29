<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Behat\Output\Node\Printer\StepPrinter;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Output\Formatter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class FakeStepPrinter implements StepPrinter
{
    public function printStep(Formatter $formatter, Scenario $scenario, StepNode $step, StepResult $result)
    {
        $formatter->getOutputPrinter()->writeln($step->getText());
    }
}
