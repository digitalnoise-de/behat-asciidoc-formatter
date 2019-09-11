<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class DumpEvents implements EventListener
{
    private $indent = -1;

    /**
     * @param Formatter $formatter
     * @param Event     $event
     * @param string    $eventName
     */
    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        $parts = explode('.', $eventName);
        if ($parts[2] === 'before') {
            ++$this->indent;
        }

        printf("%s%s (%s)\n", str_repeat('    ', $this->indent), $eventName, get_Class($event));

        if ($parts[2] === 'after') {
            --$this->indent;
        }
    }
}
