<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\FeaturePrinter;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\TaggedNodeInterface;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Behat\Testwork\Tester\Result\TestResult;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocFeaturePrinter implements FeaturePrinter
{
    /**
     * @param Formatter   $formatter
     * @param FeatureNode $feature
     */
    public function printHeader(Formatter $formatter, FeatureNode $feature)
    {
        $printer = $formatter->getOutputPrinter();

        $this->printTitle($printer, $feature);
        $this->printTags($printer, $feature);
        $this->printDescription($printer, $feature);
        $printer->writeln();
    }

    /**
     * @param OutputPrinter $printer
     * @param FeatureNode   $feature
     */
    private function printTitle(OutputPrinter $printer, FeatureNode $feature): void
    {
        $title = $feature->getTitle();
        if (empty($title)) {
            $title = $feature->getFile();
        }

        $printer->writeln(sprintf('= %s', $title));
    }

    /**
     * @param OutputPrinter $printer
     * @param FeatureNode   $feature
     */
    private function printTags(OutputPrinter $printer, FeatureNode $feature): void
    {
        if ($feature instanceof TaggedNodeInterface && $feature->hasTags()) {
            $printer->writeln(sprintf('icon:tags[] %s', implode(' ', $feature->getTags())));
        }
    }

    /**
     * @param OutputPrinter $printer
     * @param FeatureNode   $feature
     */
    private function printDescription(OutputPrinter $printer, FeatureNode $feature): void
    {
        if ($feature->hasDescription()) {
            $printer->writeln();
            $printer->writeln('****');
            $printer->writeln(implode(" +\n", explode("\n", $feature->getDescription())));
            $printer->writeln('****');
        }
    }

    /**
     * @param Formatter  $formatter
     * @param TestResult $result
     */
    public function printFooter(Formatter $formatter, TestResult $result)
    {
    }
}
