<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\EventListener;

use Behat\Behat\EventDispatcher\Event\AfterFeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeOutlineTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\EventDispatcher\Event\FeatureTested;
use Behat\Gherkin\Node\ExampleNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\EventDispatcher\Event\AfterExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Node\EventListener\EventListener;
use Behat\Testwork\Suite\Suite;
use Digitalnoise\Behat\AsciiDocFormatter\Output\AsciiDocOutputPrinter;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class SplitFiles implements EventListener
{
    /**
     * @var AsciiDocOutputPrinter
     */
    private $outputPrinter;

    public function listenEvent(Formatter $formatter, Event $event, $eventName)
    {
        $this->outputPrinter = $formatter->getOutputPrinter();

        $this->handleExercise($event);
        $this->handleSuite($event);
        $this->handleFeature($event);
        $this->handleOutline($event);
        $this->handleScenario($event);
    }

    /**
     * @param Event $event
     */
    private function handleExercise(Event $event): void
    {
        if ($event instanceof ExerciseCompleted) {
            $this->outputPrinter->setFilename('index.adoc');
        }

        if ($event instanceof AfterExerciseCompleted) {
            $suites = [];
            foreach ($event->getSpecificationIterators() as $iterator) {
                $suite = $iterator->getSuite();
                if (!in_array($suite, $suites)) {
                    $this->include($this->buildFilename($suite));

                    $suites[] = $suite;
                }
            }
        }
    }

    /**
     * @param string[] $filenames
     */
    private function include(string ...$filenames): void
    {
        $this->outputPrinter->writeln('ifndef::no-includes[]');

        foreach ($filenames as $filename) {
            $this->outputPrinter->writeln(sprintf('include::%s[]', $filename));
        }

        $this->outputPrinter->writeln('endif::[]');
    }

    /**
     * @param array $items
     *
     * @return string
     */
    private function buildFilename(...$items): string
    {
        $parts = [];

        foreach ($items as $item) {
            $parts[] = $this->basename($item);
        }

        return sprintf('%s.adoc', implode('/', $parts));
    }

    /**
     * @param $item
     *
     * @return string
     */
    private function basename($item): string
    {
        if ($item instanceof Suite) {
            return $this->cleanUp($item->getName());
        }

        if ($item instanceof FeatureNode) {
            return $this->cleanUp($item->getTitle());
        }

        if ($item instanceof ScenarioInterface) {
            return sprintf('%03d', $item->getLine());
        }

        throw new InvalidArgumentException('Unsupported argument');
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function cleanUp(string $filename): string
    {
        return strtolower(trim(preg_replace('/[^[:alnum:]-]+/', '-', $filename), '-'));
    }

    /**
     * @param Event $event
     */
    private function handleSuite(Event $event): void
    {
        if ($event instanceof SuiteTested) {
            $this->outputPrinter->setFilename($this->buildFilename($event->getSuite()));
        }

        if ($event instanceof AfterSuiteTested) {
            $filenames = [];
            foreach ($event->getSpecificationIterator() as $feature) {
                $filenames[] = $this->buildFilename($event->getSuite(), $feature);
            }

            $this->include(...$filenames);
        }
    }

    /**
     * @param Event $event
     */
    private function handleFeature(Event $event): void
    {
        if ($event instanceof FeatureTested) {
            $this->outputPrinter->setFilename($this->buildFilename($event->getSuite(), $event->getFeature()));
        }

        if ($event instanceof AfterFeatureTested) {
            $feature = $event->getFeature();

            $filenames = [];
            foreach ($feature->getScenarios() as $scenario) {
                $filenames[] = $this->buildFilename($feature, $scenario);
            }

            $this->include(...$filenames);
        }
    }

    /**
     * @param Event $event
     */
    private function handleOutline(Event $event): void
    {
        if ($event instanceof BeforeOutlineTested) {
            $this->outputPrinter->setFilename(
                $this->buildFilename($event->getSuite(), $event->getFeature(), $event->getOutline())
            );
        }
    }

    /**
     * @param Event $event
     */
    private function handleScenario(Event $event): void
    {
        if (!$event instanceof BeforeScenarioTested || $event->getScenario() instanceof ExampleNode) {
            return;
        }

        $this->outputPrinter->setFilename(
            $this->buildFilename($event->getSuite(), $event->getFeature(), $event->getScenario())
        );
    }
}
