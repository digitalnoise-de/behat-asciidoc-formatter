<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Testwork\Suite\GenericSuite;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocSuitePrinter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocSuitePrinterTest extends PrinterTestCase
{
    /**
     * @var AsciiDocSuitePrinter
     */
    private $printer;

    public function test_print_header_should_print_suite_title()
    {
        $suite = new GenericSuite('default', []);

        $this->printer->printHeader($this->formatter, $suite);

        $this->assertOutput("== default\n\n");
    }

    protected function setUp()
    {
        parent::setUp();

        $this->printer = new AsciiDocSuitePrinter();
    }
}
