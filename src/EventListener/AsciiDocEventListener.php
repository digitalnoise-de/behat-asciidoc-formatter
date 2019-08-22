<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\EventListener;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeFeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\Output\Node\Printer\FeaturePrinter;
use Behat\Behat\Output\Node\Printer\ScenarioPrinter;
use Behat\Behat\Output\Node\Printer\StepPrinter;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AsciiDocEventListener
 *
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocEventListener implements EventListener
{
    /**
     * @var FeaturePrinter
     */
    private $featurePrinter;

    /**
     * @var ScenarioPrinter
     */
    private $scenarioPrinter;

    /**
     * @var StepPrinter
     */
    private $stepPrinter;

    /**
     * @var AfterStepTested[]
     */
    private $afterStepEvents;

    /**
     * @param FeaturePrinter  $featurePrinter
     * @param ScenarioPrinter $scenarioPrinter
     * @param StepPrinter     $stepPrinter
     */
    public function __construct(
        FeaturePrinter $featurePrinter,
        ScenarioPrinter $scenarioPrinter,
        StepPrinter $stepPrinter
    ) {
        $this->featurePrinter  = $featurePrinter;
        $this->scenarioPrinter = $scenarioPrinter;
        $this->stepPrinter     = $stepPrinter;
    }

    /**
     * @param Formatter $formatter
     * @param Event     $event
     * @param string    $eventName
     */
    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        if ($event instanceof BeforeFeatureTested) {
            $this->featurePrinter->printHeader($formatter, $event->getFeature());
        }

        if ($event instanceof BeforeScenarioTested) {
            $this->scenarioPrinter->printHeader($formatter, $event->getFeature(), $event->getNode());

            $this->afterStepEvents = [];
        }

        if ($event instanceof AfterStepTested) {
            $this->afterStepEvents[] = $event;
        }

        if ($event instanceof AfterScenarioTested) {
            foreach ($this->afterStepEvents as $afterStepEvent) {
                $this->stepPrinter->printStep(
                    $formatter,
                    $event->getScenario(),
                    $afterStepEvent->getStep(),
                    $afterStepEvent->getTestResult()
                );
            }

            $this->scenarioPrinter->printFooter($formatter, $event->getTestResult());
        }
    }
}
