<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Behat\EventDispatcher\Event\BackgroundTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Symfony\Component\EventDispatcher\Event;

/**
 * Rearranges the events within an outline lifecycle:
 * Background and steps will be dispatched once before the first example
 *
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class RearrangeOutlineEvents implements EventListener
{
    /**
     * @var EventListener
     */
    private $descendant;

    /**
     * @var bool
     */
    private $inExample = false;

    /**
     * @var bool
     */
    private $firstExamplePassed = false;

    /**
     * @var bool
     */
    private $inBackground = false;

    /**
     * @var bool
     */
    private $inStep = false;

    /**
     * @var array
     */
    private $events = [];

    /**
     * @param EventListener $descendant
     */
    public function __construct(EventListener $descendant)
    {
        $this->descendant = $descendant;
    }

    /**
     * @param Formatter $formatter
     * @param Event     $event
     * @param string    $eventName
     */
    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        if ($eventName === OutlineTested::BEFORE) {
            $this->firstExamplePassed = false;
        }

        if ($eventName === ExampleTested::BEFORE) {
            $this->inExample = true;
        }

        if ($eventName === BackgroundTested::BEFORE) {
            $this->inBackground = true;
        }

        if ($eventName === StepTested::BEFORE) {
            $this->inStep = true;
        }

        if (!$this->shouldSuppress()) {
            if ($this->shouldPostpone()) {
                $this->postpone($event, $eventName);
            } else {
                $this->descendant->listenEvent($formatter, $event, $eventName);
            }
        }

        if ($eventName === StepTested::AFTER) {
            $this->inStep = false;
        }

        if ($eventName === BackgroundTested::AFTER) {
            $this->inBackground = false;
        }

        if ($eventName === ExampleTested::AFTER) {
            $this->inExample          = false;
            $this->firstExamplePassed = true;

            $this->dispatchPostponedEvents($formatter);
        }
    }

    /**
     * @return bool
     */
    private function shouldSuppress(): bool
    {
        if (!($this->inBackground || $this->inStep)) {
            return false;
        }

        return $this->inExample && $this->firstExamplePassed;
    }

    /**
     * @return bool
     */
    private function shouldPostpone(): bool
    {
        return $this->inExample && !$this->inBackground;
    }

    /**
     * @param Event  $event
     * @param string $eventName
     */
    private function postpone(Event $event, string $eventName): void
    {
        $this->events[] = [$event, $eventName];
    }

    /**
     * @param Formatter $formatter
     */
    private function dispatchPostponedEvents(Formatter $formatter): void
    {
        foreach ($this->events as $caughtEvent) {
            $this->descendant->listenEvent($formatter, $caughtEvent[0], $caughtEvent[1]);
        }

        $this->events = [];
    }
}
