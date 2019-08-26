<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Behat\Output\Node\Printer\SuitePrinter;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Behat\Testwork\EventDispatcher\Event\BeforeSuiteTested;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class SuiteListener implements EventListener
{
    /**
     * @var SuitePrinter
     */
    private $suitePrinter;

    /**
     * @param SuitePrinter $suitePrinter
     */
    public function __construct(SuitePrinter $suitePrinter)
    {
        $this->suitePrinter = $suitePrinter;
    }

    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        if ($event instanceof BeforeSuiteTested) {
            $this->suitePrinter->printHeader($formatter, $event->getSuite());
        }

        if ($event instanceof AfterSuiteTested) {
            $this->suitePrinter->printFooter($formatter, $event->getSuite());
        }
    }
}
