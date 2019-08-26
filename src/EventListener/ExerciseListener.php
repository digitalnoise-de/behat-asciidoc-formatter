<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Testwork\EventDispatcher\Event\BeforeExerciseCompleted;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocHeaderPrinter;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class ExerciseListener implements EventListener
{
    /**
     * @var AsciiDocHeaderPrinter
     */
    private $headerPrinter;

    /**
     * @param AsciiDocHeaderPrinter $headerPrinter
     */
    public function __construct(AsciiDocHeaderPrinter $headerPrinter)
    {
        $this->headerPrinter = $headerPrinter;
    }

    /**
     * @param Formatter $formatter
     * @param Event     $event
     * @param string    $eventName
     */
    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        if (!$event instanceof BeforeExerciseCompleted) {
            return;
        }

        $this->headerPrinter->printHeader($formatter);
    }
}
