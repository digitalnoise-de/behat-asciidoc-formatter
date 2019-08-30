<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\ScenarioLikeTested;
use Behat\Behat\Output\Node\Printer\ScenarioPrinter;
use Behat\Behat\Output\Node\Printer\SetupPrinter;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Testwork\EventDispatcher\Event\AfterSetup;
use Behat\Testwork\EventDispatcher\Event\AfterTested;
use Behat\Testwork\Output\Formatter;
use RuntimeException;
use Symfony\Component\EventDispatcher\Event;

class ScenarioNodeListener
{
    /**
     * @var string
     */
    private $beforeEventName;

    /**
     * @var string
     */
    private $afterEventName;

    /**
     * @var ScenarioPrinter
     */
    private $scenarioPrinter;

    /**
     * @var SetupPrinter
     */
    private $setupPrinter;

    /**
     * Initializes listener.
     *
     * @param string            $beforeEventName
     * @param string            $afterEventName
     * @param ScenarioPrinter   $scenarioPrinter
     * @param null|SetupPrinter $setupPrinter
     */
    public function __construct(
        $beforeEventName,
        $afterEventName,
        ScenarioPrinter $scenarioPrinter,
        SetupPrinter $setupPrinter = null
    ) {
        $this->beforeEventName = $beforeEventName;
        $this->afterEventName  = $afterEventName;
        $this->scenarioPrinter = $scenarioPrinter;
        $this->setupPrinter    = $setupPrinter;
    }

    /**
     * {@inheritdoc}
     */
    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        if (!($event instanceof ScenarioLikeTested || $event instanceof OutlineTested)) {
            return;
        }

        $this->printHeaderOnBeforeEvent($formatter, $event, $eventName);
        $this->printFooterOnAfterEvent($formatter, $event, $eventName);
    }

    /**
     * @param Formatter $formatter
     * @param Event     $event
     * @param string    $eventName
     */
    private function printHeaderOnBeforeEvent(Formatter $formatter, Event $event, $eventName)
    {
        if ($this->beforeEventName !== $eventName || !$event instanceof AfterSetup) {
            return;
        }

        if ($this->setupPrinter) {
            $this->setupPrinter->printSetup($formatter, $event->getSetup());
        }

        $this->scenarioPrinter->printHeader($formatter, $this->getFeature($event), $this->getScenario($event));
    }

    private function getFeature(Event $event): FeatureNode
    {
        if ($event instanceof ScenarioLikeTested || $event instanceof OutlineTested) {
            return $event->getFeature();
        }

        throw new RuntimeException(
            sprintf(
                'Expected "%s" or "%s", got "%s"',
                ScenarioLikeTested::class,
                OutlineTested::class,
                get_class($event)
            )
        );
    }

    private function getScenario(Event $event): ScenarioLikeInterface
    {
        if ($event instanceof ScenarioLikeTested) {
            return $event->getScenario();
        }

        if ($event instanceof OutlineTested) {
            return $event->getOutline();
        }

        throw new RuntimeException(
            sprintf(
                'Expected "%s" or "%s", got "%s"',
                ScenarioLikeTested::class,
                OutlineTested::class,
                get_class($event)
            )
        );
    }

    /**
     * Prints scenario/background footer on AFTER event.
     *
     * @param Formatter $formatter
     * @param Event     $event
     * @param string    $eventName
     */
    private function printFooterOnAfterEvent(Formatter $formatter, Event $event, $eventName)
    {
        if ($this->afterEventName !== $eventName || !$event instanceof AfterTested) {
            return;
        }

        if ($this->setupPrinter) {
            $this->setupPrinter->printTeardown($formatter, $event->getTeardown());
        }

        $this->scenarioPrinter->printFooter($formatter, $event->getTestResult());
    }
}
