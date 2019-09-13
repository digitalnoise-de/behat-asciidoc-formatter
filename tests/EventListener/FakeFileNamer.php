<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\EventListener;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\Suite\Suite;
use Digitalnoise\Behat\AsciiDocFormatter\Output\FileNamer;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class FakeFileNamer extends FileNamer
{
    public function nameFor(...$items): string
    {
        $parts = [];

        foreach ($items as $item) {
            if ($item instanceof Suite) {
                $parts[] = $item->getName();
            }

            if ($item instanceof FeatureNode) {
                $parts[] = $item->getTitle();
            }

            if ($item instanceof ScenarioInterface) {
                $parts[] = $item->getTitle();
            }
        }

        return implode('/', $parts);
    }
}
