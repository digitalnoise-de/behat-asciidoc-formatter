<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Testwork\Output\Formatter;
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
    private $stepEvents;

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
            $this->stepEvents = [];
        }

        if ($event instanceof AfterStepTested) {
            $this->stepEvents[] = $event;
        }

        if ($event instanceof AfterScenarioTested) {
            $stepResults = [];

            foreach ($this->stepEvents as $stepEvent) {
                $stepResults[$stepEvent->getStep()->getLine()] = $stepEvent->getTestResult();
            }

            $this->scenarioPrinter->printScenario(
                $formatter,
                $event->getFeature(),
                $event->getScenario(),
                $stepResults,
                $event->getTestResult()
            );
        }
    }
}
