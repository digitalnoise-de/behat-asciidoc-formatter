<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Behat\EventDispatcher\Event\AfterOutlineTested;
use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeOutlineTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Gherkin\Node\ExampleNode;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Digitalnoise\Behat\AsciiDocFormatter\Output\AsciiDocOutputPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocOutlinePrinter;
use PHPUnit\Framework\TestResult;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class OutlineListener implements EventListener
{
    /**
     * @var array
     */
    private $examples;

    /**
     * @var TestResult[]
     */
    private $stepResults;

    /**
     * @var AsciiDocOutlinePrinter
     */
    private $outlinePrinter;

    /**
     * @param AsciiDocOutlinePrinter $outlinePrinter
     */
    public function __construct(AsciiDocOutlinePrinter $outlinePrinter)
    {
        $this->outlinePrinter = $outlinePrinter;
    }

    /**
     * @param Formatter $formatter
     * @param Event     $event
     * @param string    $eventName
     */
    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        if ($event instanceof BeforeOutlineTested) {
            $this->examples = [];
        }

        if ($eventName === ExampleTested::BEFORE) {
            $this->stepResults = [];
        }

        if ($event instanceof AfterStepTested) {
            $this->stepResults[$event->getStep()->getLine()] = $event->getTestResult();
        }

        if ($event instanceof AfterScenarioTested && $eventName === ExampleTested::AFTER) {
            $this->examples[] = [$event->getScenario(), $this->stepResults];
        }

        if ($event instanceof AfterOutlineTested) {
            $this->printOutline($formatter, $event);
        }
    }

    /**
     * @param Formatter          $formatter
     * @param AfterOutlineTested $event
     */
    private function printOutline(Formatter $formatter, AfterOutlineTested $event): void
    {
        $this->outlinePrinter->printHeader(
            $formatter,
            $event->getFeature(),
            $event->getOutline(),
            $event->getTestResult()
        );

        foreach ($this->examples as $example) {
            /** @var ExampleNode $scenario */
            list($scenario, $stepResults) = $example;

            $this->outlinePrinter->printExample($formatter, $event->getOutline(), $scenario, $stepResults);
        }

        $this->outlinePrinter->printFooter($formatter);

        /** @var AsciiDocOutputPrinter $outputPrinter */
        $outputPrinter = $formatter->getOutputPrinter();
        $outputPrinter->pageBreak();
    }
}
