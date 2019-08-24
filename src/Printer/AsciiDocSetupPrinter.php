<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Output\Node\Printer\SetupPrinter;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Setup\Setup;
use Behat\Testwork\Tester\Setup\Teardown;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocSetupPrinter implements SetupPrinter
{
    /**
     * @param Formatter $formatter
     * @param Setup     $setup
     */
    public function printSetup(Formatter $formatter, Setup $setup)
    {
    }

    /**
     * @param Formatter $formatter
     * @param Teardown  $teardown
     */
    public function printTeardown(Formatter $formatter, Teardown $teardown)
    {
    }
}
