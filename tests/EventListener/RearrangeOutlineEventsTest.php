<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\EventListener;

use Behat\Behat\EventDispatcher\Event\BackgroundTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Digitalnoise\Behat\AsciiDocFormatter\EventListener\RearrangeOutlineEvents;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

class RearrangeOutlineEventsTest extends TestCase implements EventListener
{
    /**
     * @var array
     */
    private $events = [];

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    /**
     * @var RearrangeOutlineEvents
     */
    private $listener;

    public function test_steps_should_be_filtered_out_from_every_example_but_the_first()
    {
        $events = $this->outline(
            'o1',
            array_merge(
                $this->example('e1', $this->step('es1')),
                $this->example('e2', $this->step('es2'))
            )
        );

        $this->dispatchEvents($events);

        $this->assertEvents(
            $this->outline(
                'o1',
                array_merge(
                    $this->example('e1', $this->step('es1')),
                    $this->example('e2')
                )
            )
        );
    }

    /**
     * @param string $id
     * @param array  $children
     *
     * @return array
     */
    private function outline(string $id, array $children): array
    {
        return array_merge(
            [
                $this->event($id, OutlineTested::BEFORE),
                $this->event($id, OutlineTested::AFTER_SETUP),
            ],
            $children,
            [
                $this->event($id, OutlineTested::BEFORE_TEARDOWN),
                $this->event($id, OutlineTested::AFTER),
            ]
        );
    }

    /**
     * @param string $id
     * @param string $eventName
     *
     * @return string
     */
    private function event(string $id, string $eventName): string
    {
        return sprintf('%s:%s', $id, $eventName);
    }

    /**
     * @param string $id
     * @param array  $children
     *
     * @return array
     */
    private function example(string $id, array $children = []): array
    {
        return array_merge(
            [
                $this->event($id, ExampleTested::BEFORE),
                $this->event($id, ExampleTested::AFTER_SETUP),
            ],
            $children,
            [
                $this->event($id, ExampleTested::BEFORE_TEARDOWN),
                $this->event($id, ExampleTested::AFTER),
            ]
        );
    }

    /**
     * @param string $id
     *
     * @return array
     */
    private function step(string $id): array
    {
        return [
            $this->event($id, StepTested::BEFORE),
            $this->event($id, StepTested::AFTER_SETUP),
            $this->event($id, StepTested::BEFORE_TEARDOWN),
            $this->event($id, StepTested::AFTER),
        ];
    }

    /**
     * @param array $events
     */
    private function dispatchEvents(array $events): void
    {
        foreach ($events as $eventName) {
            list($eventId, $eventName) = explode(':', $eventName);

            $event     = new Event();
            $event->id = $eventId;

            $this->listener->listenEvent($this->formatter, $event, $eventName);
        }
    }

    /**
     * @param array $expected
     */
    private function assertEvents(array $expected): void
    {
        self::assertEquals($expected, $this->events);
    }

    public function test_background_should_be_dispatched_between_outline_and_first_example()
    {
        $events = $this->outline(
            'o1',
            $this->example(
                'e1',
                array_merge(
                    $this->background('b1', $this->step('bs')),
                    $this->step('es')
                )
            )
        );

        $this->dispatchEvents($events);

        $expected = $this->outline(
            'o1',
            array_merge(
                $this->background('b1', $this->step('bs')),
                $this->example('e1', $this->step('es'))
            )
        );

        self::assertEquals($expected, $this->events);
    }

    /**
     * @param string $id
     * @param array  $children
     *
     * @return array
     */
    private function background(string $id, array $children): array
    {
        return array_merge(
            [
                $this->event($id, BackgroundTested::BEFORE),
                $this->event($id, BackgroundTested::AFTER_SETUP),
            ],
            $children,
            [
                $this->event($id, BackgroundTested::BEFORE_TEARDOWN),
                $this->event($id, BackgroundTested::AFTER),
            ]
        );
    }

    public function test_background_should_only_be_dispatchd_once()
    {
        $events = $this->outline(
            'o1',
            array_merge(
                $this->example(
                    'e1',
                    array_merge(
                        $this->background('b1', $this->step('bs1')),
                        $this->step('es1')
                    )
                ),
                $this->example(
                    'e2',
                    array_merge(
                        $this->background('b2', $this->step('bs2')),
                        $this->step('es2')
                    )
                )
            )
        );

        $this->dispatchEvents($events);

        $expected = $this->outline(
            'o1',
            array_merge(
                $this->background('b1', $this->step('bs1')),
                $this->example('e1', $this->step('es1')),
                $this->example('e2')
            )
        );

        self::assertEquals($expected, $this->events);
    }

    public function test_background_should_be_printed_only_once_for_every_outline()
    {
        $events = array_merge(
            $this->outline(
                'o1',
                $this->example(
                    'e1',
                    array_merge(
                        $this->background('b1', $this->step('bs1')),
                        $this->step('es1')
                    )
                )
            ),
            $this->outline(
                'o2',
                $this->example(
                    'e2',
                    array_merge(
                        $this->background('b2', $this->step('bs2')),
                        $this->step('es2')
                    )
                )
            )
        );

        $this->dispatchEvents($events);

        $expected = array_merge(
            $this->outline(
                'o1',
                array_merge(
                    $this->background('b1', $this->step('bs1')),
                    $this->example('e1', $this->step('es1'))
                )
            ),
            $this->outline(
                'o2',
                array_merge(
                    $this->background('b2', $this->step('bs2')),
                    $this->example('e2', $this->step('es2'))
                )
            )
        );

        self::assertEquals($expected, $this->events);
    }

    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        $this->events[] = sprintf('%s:%s', $event->id, $eventName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->formatter = $this->createMock(Formatter::class);
        $this->listener  = new RearrangeOutlineEvents($this);
    }
}
