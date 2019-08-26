<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Output;

use Behat\Testwork\Output\Printer\Factory\FilesystemOutputFactory;
use Behat\Testwork\Output\Printer\StreamOutputPrinter;
use RuntimeException;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocOutputPrinter extends StreamOutputPrinter
{
    public function setFilename(string $filename): void
    {
        $outputFactory = $this->getOutputFactory();
        if (!$outputFactory instanceof FilesystemOutputFactory) {
            throw new RuntimeException(
                sprintf('Expected "%s", got "%s"', FilesystemOutputFactory::class, get_class($outputFactory))
            );
        }

        $path = dirname(sprintf('%s/%s', $outputFactory->getOutputPath(), $filename));
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $outputFactory->setFileName($filename);
        $this->flush();
    }
}
