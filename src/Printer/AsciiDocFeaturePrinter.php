<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\FeaturePrinter;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\TaggedNodeInterface;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Result\TestResult;

class AsciiDocFeaturePrinter implements FeaturePrinter
{
    public function printHeader(Formatter $formatter, FeatureNode $feature)
    {
        $formatter->getOutputPrinter()->writeln(sprintf('== %s', $feature->getTitle()));

        if ($feature instanceof TaggedNodeInterface && $feature->hasTags()) {
            $tagline = implode(
                ' ',
                array_map(
                    function (string $tag) {
                        return sprintf('[tag]#%s#', $tag);
                    },
                    $feature->getTags()
                )
            );

            $formatter->getOutputPrinter()->writeln($tagline);
        }

        if ($feature->hasDescription()) {
            $formatter->getOutputPrinter()->writeln('****');
            $formatter->getOutputPrinter()->writeln(implode(" +\n", explode("\n", $feature->getDescription())));
            $formatter->getOutputPrinter()->writeln('****');
        }
    }

    public function printFooter(Formatter $formatter, TestResult $result)
    {
    }
}
