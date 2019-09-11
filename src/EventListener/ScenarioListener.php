<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Testwork\Output\Formatter;
use Digitalnoise\Behat\AsciiDocFormatter\Output\AsciiDocOutputPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocScenarioPrinter;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class ScenarioListener
{
    /**
     * @var AsciiDocScenarioPrinter
     */
    private $scenarioPrinter;

    /**
     * @var StepTested[]
     */
    private $stepResults;

    /**
     * @param AsciiDocScenarioPrinter $scenarioPrinter
     */
    public function __construct(AsciiDocScenarioPrinter $scenarioPrinter)
    {
        $this->scenarioPrinter = $scenarioPrinter;
    }

    /**
     * {@inheritdoc}
     */
    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        if ($event instanceof BeforeScenarioTested) {
            $this->stepResults = [];
        }

        if ($event instanceof AfterStepTested) {
            $this->stepResults[$event->getStep()->getLine()] = $event->getTestResult();
        }

        if ($event instanceof AfterScenarioTested) {
            $this->scenarioPrinter->printScenario(
                $formatter,
                $event->getFeature(),
                $event->getScenario(),
                $this->stepResults,
                $event->getTestResult()
            );

            /** @var AsciiDocOutputPrinter $outputPrinter */
            $outputPrinter = $formatter->getOutputPrinter();
            $outputPrinter->pageBreak();
        }
    }
}
