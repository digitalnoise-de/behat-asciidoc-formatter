<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Output;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Suite\Suite;
use InvalidArgumentException;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class FileNamer
{
    /**
     * @param Suite|FeatureNode|ScenarioInterface $items,...
     *
     * @return string
     */
    public function nameFor(...$items): string
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
            return $this->cleanUp(str_replace('.feature', '', $item->getFile()));
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
}
