<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Testwork\EventDispatcher\Event\BeforeExerciseCompleted;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Digitalnoise\Behat\AsciiDocFormatter\Output\OutputDirectoryResetter;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class Resetter implements EventListener
{
    /**
     * @var OutputDirectoryResetter
     */
    private $resetter;

    /**
     * @param OutputDirectoryResetter $resetter
     */
    public function __construct(OutputDirectoryResetter $resetter)
    {
        $this->resetter = $resetter;
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

        $this->resetter->reset($formatter->getOutputPrinter()->getOutputPath());
    }
}
