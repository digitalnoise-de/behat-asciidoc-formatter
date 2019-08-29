<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class SuppressEvents implements EventListener
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
     * @var EventListener
     */
    private $descendant;

    /**
     * @var bool
     */
    private $inContext = false;

    /**
     * @param string        $beforeEventName
     * @param string        $afterEventName
     * @param EventListener $descendant
     */
    public function __construct(string $beforeEventName, string $afterEventName, EventListener $descendant)
    {
        $this->beforeEventName = $beforeEventName;
        $this->afterEventName  = $afterEventName;
        $this->descendant      = $descendant;
    }

    /**
     * {@inheritdoc}
     */
    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        if ($this->beforeEventName === $eventName) {
            $this->inContext = true;
        }

        if (!$this->inContext) {
            $this->descendant->listenEvent($formatter, $event, $eventName);
        }

        if ($this->afterEventName === $eventName) {
            $this->inContext = false;
        }
    }
}
