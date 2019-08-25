<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Suite\GenericSuite;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocSuitePrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocSuitePrinterTest extends TestCase
{
    /**
     * @var InMemoryAsciiDocOutputPrinter
     */
    private $outputPrinter;

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    /**
     * @var AsciiDocSuitePrinter
     */
    private $printer;

    public function test_print_header_should_print_suite_title()
    {
        $suite = new GenericSuite('default', []);

        $this->printer->printHeader($this->formatter, $suite);

        self::assertEquals("== default\n\n", $this->outputPrinter->getOutput());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new InMemoryAsciiDocOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->printer = new AsciiDocSuitePrinter();
    }
}
