<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\EventListener;

use Behat\Behat\EventDispatcher\Event\BeforeFeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\Output\Node\Printer\FeaturePrinter;
use Behat\Behat\Output\Node\Printer\ScenarioPrinter;
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
     * @param FeaturePrinter  $featurePrinter
     * @param ScenarioPrinter $scenarioPrinter
     */
    public function __construct(FeaturePrinter $featurePrinter, ScenarioPrinter $scenarioPrinter)
    {
        $this->featurePrinter  = $featurePrinter;
        $this->scenarioPrinter = $scenarioPrinter;
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
        }
    }
}
